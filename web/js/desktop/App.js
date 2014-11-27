/*!
 * Ext JS Library 4.0
 * Copyright(c) 2006-2011 Sencha Inc.
 * licensing@sencha.com
 * http://www.sencha.com/license
 */
Ext.define('MyDesktop.App', {
    extend: 'Ext.ux.desktop.App',
    requires: [
        'MyUtil.Path',
        'MyUtil.Message',
        'MyUtil.Object',

        'Ext.window.MessageBox',
        'Ext.ux.desktop.ShortcutModel',
        //'MyDesktop.SystemStatus',
        //'MyDesktop.VideoWindow',
        //'MyDesktop.GridWindow',
        //'MyDesktop.TabWindow',
        //'MyDesktop.AccordionWindow',
        //'MyDesktop.Notepad',
        //'MyDesktop.BogusMenuModule',
        //'MyDesktop.BogusModule',
//        'MyDesktop.Blockalanche',
        'MyDesktop.Settings',
        'SrcPageUrl.User.List',
        'SrcPageUrl.Driver.List',
        'SrcPageUrl.Customer.List',
        'SrcPageUrl.Product.List',
        'SrcPageUrl.Unit.List',
        'SrcPageUrl.Profile.Form',
        'SrcPageUrl.Liabilities.List',
        'SrcPageUrl.Invoices.List',
        'SrcPageUrl.Distributor.List',
        'SrcPageUrl.Delivery.List',
        'SrcPageUrl.Report.Main'
    ],
    init: function() {
        this.callParent();
    },
    getModules : function(){
        return [
            new MyUtil.Path(),
            new MyUtil.Message(),
            new MyUtil.Object(),
            //new MyDesktop.VideoWindow(),
            //new MyDesktop.Blockalanche(),
            //new MyDesktop.SystemStatus(),
            //new MyDesktop.GridWindow(),
            //new MyDesktop.TabWindow(),
            //new MyDesktop.AccordionWindow(),
            //new MyDesktop.Notepad(),
            //new MyDesktop.BogusMenuModule(),
            //new MyDesktop.BogusModule(),

            new SrcPageUrl.User.List(),
            new SrcPageUrl.Driver.List(),
            new SrcPageUrl.Customer.List(),
            new SrcPageUrl.Product.List(),
            new SrcPageUrl.Unit.List(),
            new SrcPageUrl.Profile.Form(),
            new SrcPageUrl.Liabilities.List(),
            new SrcPageUrl.Invoices.List(),
            new SrcPageUrl.Distributor.List(),
            new SrcPageUrl.Delivery.List(),
            new SrcPageUrl.Report.Main()
        ];
    },

    getDesktopConfig: function () {
        var me = this, ret = me.callParent();

        return Ext.apply(ret, {
            //cls: 'ux-desktop-black',
            contextMenuItems: [{ text: 'change settings'.Translator('Settings'), handler: me.onSettings, scope: me }],
            shortcuts: Ext.create('Ext.data.Store', {
                model: 'Ext.ux.desktop.ShortcutModel',
                data: this.getDataForShortcutModel()
            }),
            wallpaper: MyUtil.Path.getPathWallpaper('Blue-Sencha.jpg'),
            wallpaperStretch: false
        });
    },

    getDataForShortcutModel: function() {
        var moduleData = JSON.parse(Ext.get('ModuleJson').getAttribute('data'));

        Ext.each(moduleData, function(value, key) {
            moduleData[key].name = moduleData[key].name.Translator('Module');
        });

        return moduleData;
            //{ name: 'Notepad',          iconCls: 'notepad-shortcut',   module: 'notepad' },
            //{ name: 'System Status',    iconCls: 'cpu-shortcut',       module: 'systemstatus'}
    },

    // config for the start menu
    getStartConfig : function() {
        var me = this, ret = me.callParent(),
            jsonUserLoginData = Ext.get('UserLoginJson').getAttribute('data'),
            userLoginData     = Ext.JSON.decode(jsonUserLoginData);

        return Ext.apply(ret, {
            title: userLoginData.userName,
            iconCls: 'user',
            height: 300,
            toolConfig: {
                width: 100,
                items: [
                    {
                        text:'settings'.Translator('Settings'),
                        iconCls:'settings',
                        handler: me.onSettings,
                        scope: me
                    }, '-', {
                        text:'logout'.Translator('Logout'),
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
                { name: 'user management'.Translator('Module'), iconCls: 'user-shortcut-16', module: 'user-list' },
                { name: 'profile'.Translator('Module'), iconCls: 'profile-shortcut-16', module: 'profile-form' }
            ],
            trayItems: [{ xtype: 'trayclock', flex: 1 }]
        });
    },

    onLogout: function () {
      Ext.MessageBox.confirm('logout'.Translator('Logout'), 'are you sure you want to logout'.Translator('Logout'), function(btn){
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
