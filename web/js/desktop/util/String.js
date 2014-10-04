/**
 * Created by Hieu on 4/14/14.
 */
Ext.define('MyUtil.Path', {
    statics: {
        getPathWallpaper: function (img) {
            if (!img) {
                alert();
            }
            return 'js/src/desktop/wallpapers/'+img;
        }
    }
});