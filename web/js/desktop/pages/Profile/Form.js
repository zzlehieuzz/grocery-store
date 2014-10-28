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
            userLoginData     = JSON.parse(jsonUserLoginData),
            desktop           = this.app.getDesktop(),
            win               = desktop.getWindow('notepad');
        if(!win){
            win = desktop.createWindow({
                title: 'Profile - [' + userLoginData.userName + '}',
                width: 300,
                autoHeight: true,
                iconCls: 'notepad',
                animCollapse: false,
                border: false,
                hideMode: 'offsets',
                defaultType: 'textfield',
                layout: 'form',
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
                    id: 'profileNewPass',
                    allowBlank: false
                }, {
                    fieldLabel: 'Confirm',
                    inputType: 'password',
                    name: 'confirm',
                    vtype: 'password',
                    id: 'profileConfirm',
                    initialPassField: 'profileNewPass',
                    allowBlank: false
                }],
                buttons: [{
                    text: 'Save',
                    handler: function() {
                        var isValid = this.up('form').getForm().isValid();
                        if (isValid) {
                            var changeProfile = {
                                userId         : Ext.getCmp('userId').value,
                                profileName    : Ext.getCmp('profileName').value,
                                profileNewPass : Ext.getCmp('profileNewPass').value
                            };
                            this.up('form').getForm().reset();
                            //if (arrChangePass) {
                            //    Ext.Ajax.request({
                            //        url: MyUtil.Path.getPathAction("User_ChangePassword"),
                            //        method: 'POST',
                            //        headers: { 'Content-Type': 'application/json' },
                            //        jsonData: {'params' : arrChangePass},
                            //        scope: this,
                            //        success: function(msg) {
                            //            if (msg.status) {
                            //                console.log('success');
                            //                this.up('form').getForm().reset();
                            //                popupChangePasswordForm.hide();
                            //            }
                            //        },
                            //        failure: function(msg) {
                            //            console.log('failure');
                            //        }
                            //    });
                            //}
                        }
                    }
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

