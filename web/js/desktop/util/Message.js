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
            title = title || 'error'.Translator('Common');
            msg   = msg || 'Error default';

            Ext.MessageBox.show({
                title: title,
                msg: msg,
                buttons: Ext.MessageBox.OK,
                icon: Ext.MessageBox.ERROR
            });
        },
        MessageInfo: function (msg, title) {
            title = title || 'info'.Translator('Common');
            msg   = msg || 'Info default';
            Ext.MessageBox.show({
                title: title,
                msg: msg,
                buttons: Ext.MessageBox.OK,
                icon: Ext.MessageBox.INFO
            });
        },
        MessageQuestion: function (msg, title) {
            title = title || 'question'.Translator('Common');
            msg   = msg || 'Question default';
            Ext.MessageBox.show({
                title: title,
                msg: msg,
                buttons: Ext.MessageBox.OK,
                icon: Ext.MessageBox.QUESTION
            });
        },
        MessageWarning: function (msg, title) {
            title = title || 'warning'.Translator('Common');
            msg   = msg || 'Warning default';
            Ext.MessageBox.show({
                title: title,
                msg: msg,
                buttons: Ext.MessageBox.OK,
                icon: Ext.MessageBox.WARNING
            });
        }
    }
});