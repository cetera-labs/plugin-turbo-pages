const control = Ext.create('Plugin.turbo-pages.control', {
});

const gridDirs = Ext.create('Plugin.turbo-pages.grid', {
    flex: 1,
    from: 'dir_data',
    title: _('Список исключенных разделов'),
});

const gridMats = Ext.create('Plugin.turbo-pages.grid', {
    flex: 1,
    from: 'materials',
    title: _('Список исключенных материалов'),
});

Ext.define('Plugin.turbo-pages.Panel', {
    
    extend: 'Ext.panel.Panel',

    style: 'border: none',
    border: false,
    layout: {
        type: 'vbox',
        align: 'stretch',
    },

    items: [
        control,
        gridDirs,
        gridMats,
    ],

});