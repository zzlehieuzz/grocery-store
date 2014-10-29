/*
 * @author HieuNLD 2014/06/27
 */
Ext.define('SrcPageUrl.Profile.Form', {
    extend: 'Ext.ux.desktop.Module',
    requires: [
        'Ext.data.ArrayStore',
        'Ext.util.Format',
        'Ext.grid.Panel',
        'Ext.grid.RowNumberer'
    ],

    id:'profile-form',

    init : function(){
        this.launcher = {
            text: 'Profile form',
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
                title: 'Profile - [' + userLoginData.userName + ']',
                width: 280,
                autoHeight: true,
                iconCls: 'notepad',
                animCollapse: false,
                constrainHeader: true,
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
                            fieldLabel: 'Name',
                            name: 'name',
                            id: 'profileName',
                            value: userLoginData.name,
                            allowBlank: false
                        }, {
                            fieldLabel: 'New pass',
                            inputType: 'password',
                            name: 'newPass',
                            id: 'profileNewPass'
                        }, {
                            fieldLabel: 'Confirm',
                            inputType: 'password',
                            name: 'confirm',
                            vtype: 'password',
                            id: 'profileConfirm',
                            initialPassField: 'profileNewPass'
                        }],
                    buttons: [{
                        text: 'Save',
                        handler: function(e) {
                            var isValid = this.up('form').getForm().isValid();
                            if (isValid) {
                                var changeProfile = {
                                    userId         : Ext.getCmp('userId').value,
                                    profileName    : Ext.getCmp('profileName').value,
                                    profileNewPass : Ext.getCmp('profileNewPass').value
                                };
                                this.up('form').getForm().reset();
                                if (changeProfile) {
                                    Ext.Ajax.request({
                                        url: MyUtil.Path.getPathAction("User_ChangeProfile"),
                                        method: 'POST',
                                        headers: { 'Content-Type': 'application/json' },
                                        jsonData: {'params' : changeProfile},
                                        scope: this,
                                        success: function(msg) {
                                            if (msg.status) {
                                                var data    = Ext.JSON.decode(msg.responseText).data,
                                                    dataJson = Ext.JSON.encode(data[0]);
                                                console.log(dataJson);
                                                //Ext.get('UserLoginJson').getAttribute('data', dataJson);
                                                //console.log(obj2);
                                                //this.up('form').getForm().reset();
                                                //popupChangePasswordForm.hide();
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

