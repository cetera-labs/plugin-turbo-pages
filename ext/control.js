const eExport = Ext.create('Ext.Button', {

    text: _('Экспорт'),
    width: 100,

    handler: function() {

        const eInput = Ext.getCmp('tp-filename');
        const valueCurrent = eInput.getValue().trim();
        const filename = valueCurrent === '' ? eInput.valueDefault : valueCurrent;

        if (filename !== valueCurrent) {
            eInput.setValue(filename);
        }

        const protocol = Ext.getCmp('tp-protocol').getValue() ? '1' : '0';

        Ext.Ajax.request({
            url: '/plugins/turbo-pages/data/export.php',
            method: 'POST',
            params: {
                param: filename,
                protocol: protocol
            },
            success: function(response, options){
                console.log(response);
                const error = Ext.decode(response.responseText);
                result = error ? _('с ошибкой') : _('успешно');
                title = _('Выгрузка завершена ') + result;
                message = error ? error : filename;
                icon = error ? Ext.MessageBox.WARNING : Ext.MessageBox.INFO;
                Ext.Msg.show({  
                    title: title,
                    msg: message + '<br/>' + '<br/>',
                    icon: icon, 
                    buttons: Ext.Msg.OK
                    });
            },
            failure: function(response, options){
                alert(_('Ошибка: ') + response.statusText);
            }
        });
    },
});

const protocolInit = (eInput) => {

    Ext.Ajax.request({
        url: '/plugins/turbo-pages/data/options.php',
        method: 'POST',
        params: {
            action: 'getProtocol'
        },
        success: function(response, options){
            const result = Ext.decode(response.responseText);
            eInput.setValue(result.result);
        },
        failure: function(response, options){
            alert(_('Ошибка: ') + response.statusText);
        }
    });
}

const eProtocol = {
    xtype: 'checkbox',
    id: 'tp-protocol',
    boxLabel: 'https',
    name: 'protocol',
    initComponent: function () {
        protocolInit(this);
    },
}

const filenameInit = (eInput) => {

    Ext.Ajax.request({
        url: '/plugins/turbo-pages/data/options.php',
        method: 'POST',
        params: {
            action: 'getFilename'
        },
        success: function(response, options){
            const result = Ext.decode(response.responseText);
            eInput.setValue(result.result);
        },
        failure: function(response, options){
            alert(_('Ошибка: ') + response.statusText);
        }
    });
}

const eFileName = {

    xtype: 'textfield',
    id: 'tp-filename',
    name: 'filename',
    fieldLabel: _('Файл экспорта'),
    allowBlank: false,
    value: 'turbo-default.xml',
    valueDefalult: 'turbo-default.xml',
    flex: 1,
    initComponent: function () {
        filenameInit(this);
    },
}

Ext.define('Plugin.turbo-pages.control', {

    extend: 'Ext.toolbar.Toolbar',

    items: [
        eExport,
        '-',
        eProtocol,
        '-',
        eFileName,]

});
