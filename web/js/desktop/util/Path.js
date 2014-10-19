/**
 * Created by Hieu on 4/14/14.
 */
Ext.define('MyUtil.Path', {
    statics: {
        getPathWallpaper: function (img) {
            if (!img) {
                alert('Error image');
            }

            return 'js/desktop/wallpapers/' + img;
        },

        getPathAction: function (id) {
            if (Ext.get(id).id == id){
                return Ext.get(id).getAttribute('action');
            }

            return 'Path not exist.';
        }

    }
});