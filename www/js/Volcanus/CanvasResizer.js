/**
 * Volcanus_CanvasResizer
 *
 * createObjectURL or FileReader + Canvas で画像をリサイズします。
 *
 * 使い方の例
 * Volcanus_CanvasResizer.create({file:file, onLoad:function(image, canvas) {
 *     $("#thumbnail").html($("<img>").attr('src', canvas.toDataURL("image/png")));
 * }});
 * 
 * 参考元
 * https://www.cyberagent.co.jp/recruit/techreport/report/id=8548
 * https://developer.mozilla.org/ja/docs/Using_files_from_web_applications
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */
var Volcanus_CanvasResizer = {
    create: function(args) {
        var instance = {
            file: null
            ,maxWidth: 0
            ,maxHeight: 0
            ,floor: true
            ,objectUrl: null
            ,onLoad: null
            ,init: function(args) {
                if (typeof args.file !== 'undefined') {
                    this.file = args.file;
                }
                if (typeof args.maxWidth !== 'undefined') {
                    this.maxWidth = args.maxWidth;
                    if (typeof args.maxHeight !== 'undefined') {
                        this.maxHeight = args.maxWidth;
                    }
                }
                if (typeof args.maxHeight !== 'undefined') {
                    this.maxHeight = args.maxHeight;
                    if (typeof args.maxWidth !== 'undefined') {
                        this.maxWidth = args.maxHeight;
                    }
                }
                if (typeof args.onLoad !== 'undefined') {
                    this.onLoad = args.onLoad;
                }
                if (typeof args.floor !== 'undefined') {
                    this.floor = args.floor;
                }
                var url = window.webkitURL || window.URL;
                var createObjectURL = (url && url.createObjectURL);
                var image = new Image();
                var _self = this;
                image.onload = function() {
                    var dist = _self.getSize(image.width, image.height);
                    var canvas = document.createElement('canvas');
                    var context = canvas.getContext('2d');
                    canvas.width = dist.width;
                    canvas.height = dist.height;
                    context.drawImage(image, 0, 0, image.width, image.height, 0, 0, dist.width, dist.height);
                    if (typeof _self.onLoad !== 'undefined') {
                        _self.onLoad(image, canvas);
                    }
                    if (url && _self.objectUrl) {
                        url.revokeObjectURL(_self.objectUrl);
                    }
                };
                if (typeof createObjectURL !== 'undefined') {
                    image.src = this.objectUrl = url.createObjectURL(this.file);
                } else {
                    var reader = new FileReader();
                    reader.onload = function(event) {
                        image.src = event.target.result;
                    }
                    reader.readAsDataURL(this.file);
                }
                return this;
            }
            ,getSize: function(srcWidth, srcHeight) {
                var dist = {
                    width: srcWidth
                    ,height: srcHeight
                }
                if ((this.maxWidth > 0 && srcWidth > this.maxWidth) || (this.maxHeight > 0 && srcHeight > this.maxHeight)) {
                    var w_percent = (100 * this.maxWidth) / srcWidth;
                    var h_percent = (100 * this.maxHeight) / srcHeight;
                    if (w_percent < h_percent) {
                        dist.width = this.maxWidth;
                        dist.height = (this.floor)
                            ? Math.floor((srcHeight * w_percent) / 100)
                            : Math.ceil((srcHeight * w_percent) / 100);
                    } else {
                        dist.width = (this.floor)
                            ? Math.floor((srcWidth * h_percent) / 100)
                            : Math.ceil((srcWidth * h_percent) / 100);
                        dist.height = this.maxHeight;
                    }
                    if (dist.width < 1) {
                        dist.width = 1;
                    }
                    if (dist.height < 1) {
                        dist.height = 1;
                    }
                }
                return dist;
            }
        }
        instance.init(args);
        return instance;
    }
};
