/*
 * @author HieuNLD 2014/06/27
 */
var readerJson = {
    type: 'json',
    root: 'data',
    id  : 'id'
};

var objectField = [{name: 'id',       type: 'int'},
                   {name: 'userName', type: 'string'},
                   {name: 'name',     type: 'string'},
                   {name: 'email',    type: 'string'}];

function defineModel (modelName, objectField) {
    Ext.define(modelName, {
        extend: 'Ext.data.Model',
        fields: objectField
    });
}

defineModel('User', objectField);

function getPathAction (id, action) {
    action = action || "action";

    return Ext.get(id).getAttribute(action);
}

var storeAllPlayer = new Ext.data.JsonStore({
    model: 'User',
    proxy: new Ext.data.HttpProxy({
        url: getPathAction("Common_LoadPlayer"),
        reader: readerJson
    }),
    autoLoad: true
});

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
            text: 'User List',
            iconCls:'icon-grid'
        };
    },

    createWindow : function(){
        var desktop = this.app.getDesktop();
        var win = desktop.getWindow('grid-win');
        if(!win){
            win = desktop.createWindow({
                id: 'user-list',
                title:'User List',
                width:740,
                height:480,
                iconCls: 'icon-grid',
                animCollapse:false,
                constrainHeader:true,
                layout: 'fit',
                items: [
                    {
                        border: false,
                        xtype: 'grid',
                        store: storeAllPlayer,
                        columns: [
                            new Ext.grid.RowNumberer(),
                            {
                              text: "User Name",
                              width: 150,
                              flex: 1,
  //                                sortable: true,
                              dataIndex: 'userName'
                            }, {
                                text: "Name",
//                                width: 150,
                                flex: 2,
                                dataIndex: 'name'
                            }, {
                                text: "Email",
                                flex: 3,
                                dataIndex: 'email'
                            }
                        ]
                    }
                ],
                tbar:[{
                    text:'Add Something',
                    tooltip:'Add a new row',
                    iconCls:'add'
                }, '-', {
                    text:'Options',
                    tooltip:'Modify options',
                    iconCls:'option'
                },'-',{
                    text:'Remove Something',
                    tooltip:'Remove the selected item',
                    iconCls:'remove'
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

