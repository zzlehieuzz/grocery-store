/*
 * @author HieuNLD 2014/06/27
 */
var readerJson = {
    type: 'json',
    root: 'data',
    id  : 'id',
    totalProperty: 'total'
};

var objectField = [{name: 'id',        type: 'int'},
                     {name: 'userName', type: 'string'},
                     {name: 'name',      type: 'string'},
                     {name: 'email',     type: 'string'}];

Ext.define('SrcPageUrl.User.List', {
    extend: 'Ext.ux.desktop.Module',
    requires: [
        'Ext.data.ArrayStore',
        'Ext.util.Format',
        'Ext.grid.Panel',
        'Ext.grid.RowNumberer'
    ],

    id:'user-list',

    init : function(){
        this.launcher = {
            text: 'user management'.Translator('Module'),
            iconCls:'icon-grid'
        };

        MyUtil.Object.defineModel('User', objectField);
    },

    createWindow : function(){


        var storeLoadUser = new Ext.data.JsonStore({
            model: 'User',
            proxy: new Ext.data.HttpProxy({
                url: MyUtil.Path.getPathAction("User_Load"),
                reader: readerJson
            }),
            pageSize: pageSizeDefault,
            autoLoad: ({params:{limit: limitDefault, page: pageDefault, start: startDefault}}, false)
        });

        var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
            clicksToMoveEditor: 1,
            autoCancel: false,
            listeners: {
                edit: function (editor, e) {
                    var record = e.record.data;

                    Ext.Ajax.request({
                        url: MyUtil.Path.getPathAction("User_Update"),
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        waitTitle: 'processing'.Translator('Common'),
                        waitMsg: 'sending data'.Translator('Common'),
                        jsonData: {'params' : record},
                        scope: this,
                        success: function(msg) {
                            if (msg.status) {
                                storeLoadUser.reload();
                            }
                        },
                        failure: function(msg) {
                            console.log('failure');
                        }
                    });
                }
            }
        });

        Ext.apply(Ext.form.VTypes, {
            password: function(val, field) {
                if (field.initialPassField) {
                    var pwd = field.up('form').down('#' + field.initialPassField);
                    return (val == pwd.getValue());
                }
                return true;
            },

            passwordText: 'Passwords do not match'
        });

        var changePasswordForm = Ext.widget({
            xtype: 'form',
            layout: 'form',
            frame: true,
            border: false,
            style: 'border: 0;',
            width: 350,
            fieldDefaults: {
                msgTarget: 'side',
                labelWidth: 75
            },
            defaultType: 'textfield',
            items: [{
                xtype: 'hidden',
                name: 'userId',
                id: 'changeUserId',
                allowBlank: false
            }, {
                fieldLabel: 'new-pass'.Translator('User'),
                inputType: 'password',
                name: 'newPass',
                id: 'changeNewPass',
                allowBlank: false
            }, {
                fieldLabel: 'confirm-pass'.Translator('User'),
                inputType: 'password',
                name: 'confirm',
                vtype: 'password',
                id: 'changeConfirm',
                initialPassField: 'changeNewPass',
                allowBlank: false
            }],

            buttons: [{
                text: 'save'.Translator('Common'),
                handler: function() {
                    var isValid = this.up('form').getForm().isValid();
                    if (isValid) {
                        var arrChangePass = {
                            id     : Ext.getCmp('changeUserId').value,
                            newPass: Ext.getCmp('changeNewPass').value
                        };

                        if (arrChangePass) {
                            Ext.Ajax.request({
                                url: MyUtil.Path.getPathAction("User_ChangePassword"),
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                jsonData: {'params' : arrChangePass},
                                waitTitle: 'processing'.Translator('Common'),
                                waitMsg: 'sending data'.Translator('Common'),
                                scope: this,
                                success: function(msg) {
                                    if (msg.status) {
                                        this.up('form').getForm().reset();
                                        popupChangePasswordForm.hide();
                                    }
                                },
                                failure: function(msg) {
                                    console.log('failure');
                                }
                            });
                        }
                    }
                }
            }, {
                text: 'cancel'.Translator('Common'),
                handler: function() {
                    this.up('form').getForm().reset();
                    popupChangePasswordForm.hide();
                }
            }]
        });

        var popupChangePasswordForm = new Ext.Window({
            title: 'change-pass'.Translator('User')
            , autoWidth: true
            , autoHeight: true
            , border: true
            , modal: true
            , items: changePasswordForm
        });

        var rowModel = Ext.create('Ext.selection.RowModel', {
            mode : "MULTI",
            onKeyPress: function(e, t) {
                console.log(e);
            }
        });

        var columnsUser = [
            new Ext.grid.RowNumberer(),
            {
                dataIndex: 'id',
                hidden : true
            }, {
                text: "user-name".Translator('User'),
                width: 150,
                dataIndex: 'userName',
                editor: {
                    xtype: 'textfield'
                }
            }, {
                text: "name".Translator('Common'),
                flex: 1,
                dataIndex: 'name',
                editor: {
                    xtype: 'textfield'
                }
            }, {
                text: "email".Translator('User'),
                flex: 2,
                dataIndex: 'email',
                editor: {
                    xtype: 'textfield'
                }
            }
        ];

        var desktop = this.app.getDesktop();
        var win = desktop.getWindow('grid-win');
        if(!win){
            win = desktop.createWindow({
                id: 'user-list',
                title: 'user management'.Translator('Module'),
                width:740,
                height:480,
                iconCls: 'icon-grid',
                animCollapse: false,
                constrainHeader: true,
                layout: 'fit',
                items:
                    [{
                        border: false,
                        xtype: 'grid',
                        id: 'grid-user-list',
                        store: storeLoadUser,
                        loadMask:true,
                        selModel: rowModel,
                        plugins: rowEditing,
                        columns: columnsUser,
                        listeners:{
                            beforerender: function () {
                                this.store.load();
                            }
                        }
                    }
                ],
                tbar:[{
                    text: 'add'.Translator('Common'),
                    tooltip: 'add'.Translator('Common'),
                    iconCls:'add',
                    handler : function() {
                      rowEditing.cancelEdit();

                      // Create a model instance
                      var r = Ext.create('User', {
                        id: '',
                        userName: '',
                        name: '',
                        email: ''
                      });

                      storeLoadUser.insert(0, r);
                      rowEditing.startEdit(0, 0);
                    }
                    }, '-', {
                        text:'change-pass'.Translator('User'),
                        tooltip:'change-pass'.Translator('User'),
                        iconCls:'edit',
                        handler : function() {
                            var selection = Ext.getCmp('grid-user-list').getView().getSelectionModel().getSelection();

                            if (selection.length == 1) {
                                if(selection[0].data.id) {
                                    popupChangePasswordForm.show();
                                    changePasswordForm.getForm().setValues({ changeUserId: selection[0].data.id });
                                } else {
                                  MyUtil.Message.MessageWarning('please choose 1 record has data'.Translator('Common'));
                                }
                            } else {
                                MyUtil.Message.MessageWarning('please choose 1 record to change password'.Translator('User'));
                            }
                        }
                    }, '-',{
                    text: 'remove'.Translator('Common'),
                    tooltip: 'remove'.Translator('Common'),
                    iconCls:'remove',
                    listeners: {
                        click: function () {
                            var selection = Ext.getCmp('grid-user-list').getView().getSelectionModel().getSelection();

                            if (selection.length > 0) {
                                Ext.MessageBox.confirm('delete'.Translator('Common'), 'are you sure'.Translator('Common'), function(btn) {
                                    if(btn === 'yes') {
                                        var arrId = [];
                                        Ext.each(selection, function(v, k) {
                                            arrId[k] = v.data.id;
                                        });

                                        Ext.Ajax.request({
                                            url: MyUtil.Path.getPathAction("User_Delete"),
                                            method: 'POST',
                                            headers: { 'Content-Type': 'application/json' },
                                            jsonData: {'params' : arrId},
                                            waitTitle: 'processing'.Translator('Common'),
                                            waitMsg: 'sending data'.Translator('Common'),
                                            scope: this,
                                            success: function(msg) {
                                                if (msg.status) {
                                                    storeLoadUser.reload();
                                                }
                                            },
                                            failure: function(msg) {
                                                console.log('failure');
                                            }
                                        });
                                    }
                                });
                            } else {
                                MyUtil.Message.MessageWarning('please choose 1 record to delete'.Translator('Common'));
                            }
                        }
                    }
                }],
                bbar: new Ext.PagingToolbar({
                    store: storeLoadUser,
                    displayInfo:true
                })
            });
        }
        return win;
    },

    statics: {
        getDummyData: function () {
            return [];
        }
    }
});

