const listDirs = Ext.create('Cetera.field.DirSet', {
    from: '0',
    height: 230,
    name: 'dir_data',
});

const listMaterials = Ext.create('Cetera.field.LinkSet', {
    from: '0',
    height: 230,
    name: 'materials',
});

const setToArray = (set) => {
    if (set === '[]') {
        return [];
    }
    const raw = set.replace('[', '').replace(']', '').replaceAll('"', '');
    const arrRaw = raw.split(',');
    const arr = arrRaw.map( (value) => {
        return Number(value);
    });
    return arr;
}

const structure = () => {
    return new Map([
        ['dir_data', listDirs],
        ['materials', listMaterials],
    ]);
}

Ext.define('Plugin.turbo-pages.Selector', {
 
    extend: 'Ext.Window',

    closeAction: 'hide',
    width: 510,
    height: 300,
    layout: 'vbox',
    modal: true,
    resizable: false,
    border: false,

    initComponent: function () {

        from = this.from;
        switch (from) {
            case 'dir_data':
                titleEnd = 'разделы';
                break;
            case 'materials':
                titleEnd = 'материалы';
                break;
            default:
                titleEnd = ''
        }
        this.title = 'Выбор исключений: ' + titleEnd;

        this.form = Ext.create('Ext.form.FormPanel', {
            layout: {
                type: 'vbox',
                align : 'stretch',
            },               
            border: false,
            width: 500,
            bodyStyle: 'background: none',
            waitMsgTarget: true,
            items: [
                structure().get(from),
            ],
        });

        this.items = this.form;

        this.buttons = [{
            text: 'OK',
            scope: this,
            handler: this.submit
        }, {
            text: 'Отмена',
            scope: this,
            handler: function () {
                this.hide();
            }
        }];

        this.callParent();
    },

    submit: function () {

        const values = structure().get(from).getValue();
        const addition = JSON.stringify(setToArray(values));
        Ext.Ajax.request({
            url: '/plugins/turbo-pages/data/options.php',
            method: 'POST',
            params: {
                action: 'setList',
                from: this.from,
                param: addition,
            },
            success: function(response, options){
                // const result = Ext.decode(response.responseText);
                // console.log(result);
            },
            failure: function(response, options){
                alert("Ошибка: " + response.statusText);
            }
        });

        this.fireEvent('listChanged');
        this.hide();
    }
});