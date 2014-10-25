/**
 * Created by Hieu on 4/14/14.
 */
Ext.define('MyUtil.Object', {
    statics: {
        defineModel: function (modelName, objectField) {
            Ext.define(modelName, {
                extend: 'Ext.data.Model',
                fields: objectField
            });
        }
    }
});