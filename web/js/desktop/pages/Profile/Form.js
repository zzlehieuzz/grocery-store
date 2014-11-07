/*
 * @author HieuNLD 2014/06/27
 */
Ext.define('SrcPageUrl.Profile.Form', {
    extend: 'Ext.ux.desktop.Module',
    requires: [

    ],

    id:'profile-form',

    init : function(){
        this.launcher = {
            text: 'profile'.Translator('Module'),
            iconCls: 'notepad'
        };
    },

    createWindow : function(){
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

        var jsonUserLoginData = Ext.get('UserLoginJson').getAttribute('data'),
            userLoginData     = Ext.JSON.decode(jsonUserLoginData),
            desktop           = this.app.getDesktop(),
            win               = desktop.getWindow('notepad');
        if(!win){
            win = desktop.createWindow({
                title: 'profile'.Translator('Module') +' - [' + userLoginData.userName + ']',
                width: 280,
                autoHeight: true,
                iconCls: 'notepad',
                animCollapse: false,
                constrainHeader: false,
                id: 'profileForm',
                items: [{
                            xtype: 'form',
                            bodyPadding: 5,
                            defaultType: 'textfield',
                            border: false,
                            items: [{
                            xtype: 'hidden',
                            name: 'userId',
                            id: 'userId',
                            value: userLoginData.id,
                            allowBlank: false
                        }, {
                            fieldLabel: 'name'.Translator('Common'),
                            name: 'name',
                            id: 'profileName',
                            value: userLoginData.name,
                            allowBlank: false
                        }, {
                            fieldLabel: 'new-pass'.Translator('Profile'),
                            inputType: 'password',
                            name: 'newPass',
                            id: 'profileNewPass'
                        }, {
                            fieldLabel: 'confirm-pass'.Translator('Profile'),
                            inputType: 'password',
                            name: 'confirm',
                            vtype: 'password',
                            id: 'profileConfirm',
                            initialPassField: 'profileNewPass'
                        }],
                    buttons: [{
                        text: 'save'.Translator('Common'),
                        handler: function(e) {
                            var isValid = this.up('form').getForm().isValid();
                            if (isValid) {
                                var changeProfile = {
                                    userId         : Ext.getCmp('userId').value,
                                    profileName    : Ext.getCmp('profileName').value,
                                    profileNewPass : Ext.getCmp('profileNewPass').value
                                };
                                if (changeProfile) {
                                    Ext.Ajax.request({
                                        url: MyUtil.Path.getPathAction("User_ChangeProfile"),
                                        method: 'POST',
                                        headers: { 'Content-Type': 'application/json' },
                                        jsonData: {'params' : changeProfile},
                                        waitTitle: 'processing'.Translator('Common'),
                                        waitMsg: 'sending data'.Translator('Common'),
                                        scope: this,
                                        success: function(msg) {
                                            if (msg.status) {
                                                var data     = Ext.JSON.decode(msg.responseText).data,
                                                    dataJson = Ext.JSON.encode(data[0]);

                                                Ext.getCmp('userId').setValue(data[0].id);
                                                Ext.getCmp('profileName').setValue(data[0].name);

                                                var attrData = {'data': dataJson};
                                                Ext.get('UserLoginJson').set(attrData);
                                                MyUtil.Message.MessageInfo('save successfully'.Translator('Common'));
                                                win.doClose();
                                            }
                                        },
                                        failure: function(msg) {
                                            console.log('failure');
                                        }
                                    });
                                }
                            }
                        }
                    }]
                }]
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

