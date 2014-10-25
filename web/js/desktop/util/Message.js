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
                msg: msg,
                buttons: Ext.MessageBox.OK,
                icon: Ext.MessageBox.ERROR
            });
        },
        MessageInfo: function (msg, title) {
            title = title || 'Info';
            msg   = msg || 'Info messsage';
            Ext.MessageBox.show({
                title: title,
                msg: msg,
                buttons: Ext.MessageBox.OK,
                icon: Ext.MessageBox.INFO
            });
        },
        MessageQuestion: function (msg, title) {
            title = title || 'Question';
            msg   = msg || 'Question messsage';
            Ext.MessageBox.show({
                title: title,
                msg: msg,
                buttons: Ext.MessageBox.OK,
                icon: Ext.MessageBox.QUESTION
            });
        },
        MessageWarning: function (msg, title) {
            title = title || 'Warning';
            msg   = msg || 'Warning messsage';
            Ext.MessageBox.show({
                title: title,
                msg: msg,
                buttons: Ext.MessageBox.OK,
                icon: Ext.MessageBox.WARNING
            });
        }
    }
});