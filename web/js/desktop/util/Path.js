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

        getActionPath: function (routing) {
            if (!routing) {
                alert('Routing is not null');
            }

//            console.log(path);
            return '{{ path('+routing+') }}';
        }


    }
});