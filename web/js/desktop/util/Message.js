/**
 * Created by Hieu on 4/14/14.
 *    Ext.fly('info').dom.value = Ext.MessageBox.INFO;
 Ext.fly('question').dom.value = Ext.MessageBox.QUESTION;
 Ext.fly('warning').dom.value = Ext.MessageBox.WARNING;
 Ext.fly('error').dom.value = Ext.MessageBox.ERROR;
 */
Ext.define('MyUtil.Message', {
    statics: {
        MessageError: function (msg, title) {
            title = title || 'Error';
            msg   = msg || 'Error messsage';

            Ext.MessageBox.show({
                title: title,
                msg: 'MessageError',
                buttons: Ext.MessageBox.OK,
                icon: Ext.MessageBox.ERROR
            });
        },
        MessageInfo: function (msg, title) {
            Ext.MessageBox.show({
                title: title,
                msg: msg,
                buttons: Ext.MessageBox.OK,
                icon: Ext.MessageBox.INFO
            });
        },
        MessageQuestion: function (msg, title) {
            Ext.MessageBox.show({
                title: 'Stub Generation',
                msg: 'Generation Successful',
                buttons: Ext.MessageBox.OK,
                icon: Ext.MessageBox.QUESTION
            });
        },
        MessageWarning: function (msg, title) {
            Ext.MessageBox.show({
                title: 'Stub Generation',
                msg: 'Generation Successful',
                buttons: Ext.MessageBox.OK,
                icon: Ext.MessageBox.WARNING
            });
        }
    }
});