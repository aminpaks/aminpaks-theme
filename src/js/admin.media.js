/* 
 *  @license © 2014, Amin Paks, T. (514) 441-2413, W. http://www.aminpaks.com
 */
(function (_w, _c, $) {
    'use strict';

    var wp, l10n;
    var handler = _w.___site_admin_handler = {
        contents: {
            mediaUploaderInit: function () {
                $('.media-uploader').each(function () {
                    return mediaUploader(this);
                });
            }
        },
        init: function () {
            wp = _w.wp;
            l10n = wp.media && wp.media.view.l10n || {};
            handler.contents.mediaUploaderInit();
        }
    };

    var mediaUploader = _w.___media_uploader = function (dom) {
        return new mediaUploader.fn.init(dom);
    };
    mediaUploader.fn = mediaUploader.prototype = {
        id: null,
        _owner: null,
        _frame: null,
        label: function () {
            if (this._label) {
                return this._label;
            }
            return this._label = this._owner.find('.media-uploader-input');
        },
        button: function () {
            if (this._button) {
                return this._button;
            }
            return this._button = this._owner.find('.media-uploader-button');
        },
        input: function () {
            if (this._input) {
                return this._input;
            }
            return this._input = this._owner.find('.media-uploader-input-value');
        },
        frame: function () {
            var _this = this;

            if (this._frame)
                return this._frame;

            this._frame = wp.media.frames[this.id] = wp.media({
                id: this.id,
                //frame: 'post',
                //state: 'insert',
                title: l10n.addMedia,
                button: {
                    text: l10n.insertIntoPost,
                },
                //editing: false,
                multiple: false,
            });

            this._frame.on('select', function () {
                _this.onSelect.call(_this, _this._frame);
            });

            return this._frame;
        },
        onSelect: function () {
            var attachment = this.frame().state().get('selection').first().toJSON();

            this.label().text(attachment.filename);
            this.input().val(attachment.url);
            _c.log(attachment, this.input());

            return true;
        }
    };
    var mediaUploaderInit = mediaUploader.fn.init = function (dom) {
        var _this = this,
                el = _this._owner = $(dom),
                id = _this.id = el.attr('data-id'),
                button = _this._button = el.find('.media-uploader-button');

        button.on('click', function (event) {
            event.preventDefault();

            _this.frame().open();
        });

        wp.media[id] = _this;
        _c.log(wp.media.editor.send.attachment);

        return _this;
    };
    mediaUploaderInit.prototype = mediaUploader.fn;

    $(handler.init);
})(window, console, jQuery);