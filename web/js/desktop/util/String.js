/**
 * Created by Hieu on 4/14/14.
 */
Ext.define('MyUtil.String', {
    statics: {
        createTplCombo: function (firstValue, secondValue) {
            var style      = 'float: left;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;',
                styleLeft  = style+'width:120px;',
                styleRight = style+'width:280px;';

            return '<tpl for=".">' +
            '<div class="x-boundlist-item" style="font-size:13px;">'+
            '<div style="'+styleLeft+'">&nbsp{'+firstValue+'}</div>' +
            '<div style="float: left; width: 20px; text-align: center;">|</div>' +
            '<div style="'+styleRight+'">&nbsp{'+secondValue+'}</div>' +
            '<div style="clear: both;"></div>'+
            '</div>' +
            '</tpl>';
        }
    }
});