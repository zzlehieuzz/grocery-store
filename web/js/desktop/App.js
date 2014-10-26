/*!
 * Ext JS Library 4.0
 * Copyright(c) 2006-2011 Sencha Inc.
 * licensing@sencha.com
 * http://www.sencha.com/license
 */

Ext.define('MyDesktop.App', {
    extend: 'Ext.ux.desktop.App',

    requires: [
        'Ext.window.MessageBox',
        'Ext.ux.desktop.ShortcutModel',
        'MyDesktop.SystemStatus',
        'MyDesktop.VideoWindow',
        'MyDesktop.GridWindow',
        'MyDesktop.TabWindow',
        'MyDesktop.AccordionWindow',
        'MyDesktop.Notepad',
        'MyDesktop.BogusMenuModule',
        'MyDesktop.BogusModule',
//        'MyDesktop.Blockalanche',
        'MyDesktop.Settings',

        'MyUtil.Path',
        'MyUtil.Message',
        'MyUtil.Object',

        'SrcPageUrl.User.List',
        'SrcPageUrl.Driver.List',
        'SrcPageUrl.Customer.List',
        'SrcPageUrl.Product.List',
        'SrcPageUrl.Unit.List',
    ],

    init: function() {
        // custom logic before getXYZ methods get called...

        this.callParent();

        // now ready...
    },

    getModules : function(){
        return [
            new MyDesktop.VideoWindow(),
            //new MyDesktop.Blockalanche(),
            new MyDesktop.SystemStatus(),
            new MyDesktop.GridWindow(),
            new MyDesktop.TabWindow(),
            new MyDesktop.AccordionWindow(),
            new MyDesktop.Notepad(),
            new MyDesktop.BogusMenuModule(),
            new MyDesktop.BogusModule(),

            new SrcPageUrl.User.List(),
            new SrcPageUrl.Driver.List(),
            new SrcPageUrl.Customer.List(),
            new SrcPageUrl.Product.List(),
            new SrcPageUrl.Unit.List()
        ];
    },

    getDesktopConfig: function () {
        var me = this, ret = me.callParent();

        return Ext.apply(ret, {
            //cls: 'ux-desktop-black',
            contextMenuItems: [{ text: 'Change Settings', handler: me.onSettings, scope: me }],
            shortcuts: Ext.create('Ext.data.Store', {
                model: 'Ext.ux.desktop.ShortcutModel',
                data: this.getDataForShortcutModel()
            }),
            wallpaper: MyUtil.Path.getPathWallpaper('Blue-Sencha.jpg'),
            wallpaperStretch: false
        });
    },

    getDataForShortcutModel: function() {
        var jsonModuleData = Ext.get('Common_LoadModule').getAttribute('data');

        return JSON.parse(jsonModuleData);
            //{ name: 'Notepad',          iconCls: 'notepad-shortcut',   module: 'notepad' },
            //{ name: 'System Status',    iconCls: 'cpu-shortcut',       module: 'systemstatus'}

    },

    // config for the start menu
    getStartConfig : function() {
        var me = this, ret = me.callParent();

        return Ext.apply(ret, {
            title: 'Don Griffin',
            iconCls: 'user',
            height: 300,
            toolConfig: {
                width: 100,
                items: [
                    {
                        text:'Settings',
                        iconCls:'settings',
                        handler: me.onSettings,
                        scope: me
                    }, '-', {
                        text:'Logout',
                        iconCls:'logout',
                        handler: me.onLogout,
                        scope: me
                    }
                ]
            }
        });
    },

    getTaskbarConfig: function () {
        var ret = this.callParent();

        return Ext.apply(ret, {
            quickStart: [
                { name: 'Accordion Window', iconCls: 'accordion', module: 'acc-win' },
                { name: 'Grid Window', iconCls: 'icon-grid', module: 'grid-win' }
            ],
            trayItems: [
                { xtype: 'trayclock', flex: 1 }
            ]
        });
    },

    onLogout: function () {
      Ext.MessageBox.confirm('Logout', 'Are you sure you want to logout?', function(btn){
        if (btn === 'yes'){
          Ext.Ajax.request({
            url: MyUtil.Path.getPathAction("user_logout")
            , params: null
            , method: 'POST'
            , success: function (data) {
              location.reload();
            }
          });
        }
      });
    },

    onSettings: function () {
        var dlg = new MyDesktop.Settings({
            desktop: this.desktop
        });
        dlg.show();
    }
});
