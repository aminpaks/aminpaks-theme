
(function (_w, _c, $) {
    'use strict';

    var
            __w = $(_w),
            __b = null,
            __h = null,
            __hb = null,
            __mobile = null,
            __minimal = null,
            __touch = null,
            _theme,
            _nav,
            _header,
            _content,
            _footer;

    var log = function () {
        try {
            _c.log.apply(_c, arguments);
        } catch (e) {
        }
    };
    var core = {
        auto: {
            toTOP: function () {
                var scroll_y, window_h, footer, footer_height, footer_y, top;

                footer = __b.find('.theme > .footer');
                top = footer.find('.scroll-to-top');

                __w.on('resize', function () {
                    footer_y = footer.offset().top;
                    footer_height = footer.outerHeight(true);
                    window_h = __w.height();

                    if (footer_y > window_h * 1.2) {
                        if (top.hasClass('invisible')) {
                            top.removeClass('invisible');
                        }
                    } else {
                        if (!top.hasClass('invisible')) {
                            top.addClass('invisible');
                        }
                    }
                }).trigger('resize');

                __w.on('scroll', function () {
                    scroll_y = __w.scrollTop();

                    if (footer_y > scroll_y + window_h) {
                        if (scroll_y > window_h / 4) {
                            if (!top.hasClass('sticky')) {
                                top.addClass('sticky');
                            }
                        } else {
                            if (top.hasClass('sticky')) {
                                top.removeClass('sticky');
                            }
                        }
                    } else {
                        if (top.hasClass('sticky')) {
                            top.removeClass('sticky');
                        }
                    }
                }).sleep(500, function () {
                    __w.trigger('scroll');
                }).sleepExec();

                top.on('click', function () {
                    __b.animate({'scrollTop': 0}, 200);
                });
            },
            xNav: function () {
                var window_h, scroll_y, xnav, clean;//, sections, active;

                xnav = __b.find('.x-navigation');
                clean = function () {
                    var $this = $(this);
                    if ($this.hasClass('sticky')) {
                        $this.removeClass('sticky').parents('.x-nav-sticky').removeClass('x-nav-sticky').css({'padding-top': ''});
                        $this.data('top', $this.offset().top);
                        $this.data('height', $this.outerHeight(true));
                    }
                };
                //sections = __b.find('.section');
                //log('section', sections);

                __w.on('resize', function () {

                    if (__mobile) {
                        return;
                    }

                    xnav.each(clean);

                    window_h = __w.height();
                }).trigger('resize');

                xnav.each(function () {
                    var $this = $(this);
                    $this.data('top', $this.offset().top);
                    $this.data('height', $this.outerHeight(true));
                });

                __w.on('scroll', function () {

                    if (__mobile) {
                        return;
                    }

                    scroll_y = __w.scrollTop();

                    xnav.each(function () {
                        var $this = $(this), top, height, parent;

                        top = $this.data('top');
                        height = $this.data('height');

                        if (top < scroll_y) {
                            if (!$this.hasClass('sticky')) {
                                $this.addClass('sticky');
                                parent = $this.parents('.section');
                                if (parent.size() <= 0) {
                                    parent = $this.parents('.container');
                                }
                                if (parent.size()) {
                                    parent.addClass('x-nav-sticky').css({'padding-top': $this.data('height')});
                                }
                            }
                        } else {
                            clean.call(this);
                        }
                    });
                });
            },
            content: function () {
                if (__mobile) {
                    return;
                }
            },
            navigation: function () {
                var top, header, text, face, func;

                text = _header.find('.top-face .text');
                face = _header.find('.top-face .face');

                _nav.data('hangToBottom', true);

                setTimeout(function () {
                    if (!__b.hasClass('home')) {
                        if (header.height() > __w.height()) {
                            header.css({'height': __w.height() - 80, 'min-height': 'initial'});
                        } else {
                            header.css({'height': '', 'min-height': ''});
                        }
                    }
                }, 200);

                func = function () {
                    header = _header.find('.header-wrapper');
                    if (__b.hasClass('home') && header.css('min-height').toInt() !== __w.height() - 80) {
                        header.css({'min-height': __w.height() - 80});

                        text.css({'margin-top': (header.height() - text.height()) / 2});
                    }

                    top = __w.scrollTop();

                    if (top > _header.height()) {
                        //_nav.css({'top': _header.height() - top}).removeClass('minimized').data('hangToBottom', true);
                        _nav.css({'top': ''});

                        if (!__b.hasClass('nav-fixed')) {
                            __b.addClass('nav-fixed');
                        }
                    } else {
                        if (__b.hasClass('nav-fixed')) {
                            __b.removeClass('nav-fixed');
                        }
                    }
                };

                __w.on('scroll resize', func).trigger('resize');
            }
        },
        once: {
        },
        autoBody: {
            home: function () {
                _header.find('[href="#intro"]').on('click', function (e) {
                    __b.animate({'scrollTop': _nav.offset().top}, 500);

                    e.preventDefault;
                    return false;
                });
            },
        },
        execAuto: function () {
            $.each(core.auto, function () {
                if (typeof this === 'function') {
                    this.apply(core);
                }
            });
        },
        init: function () {
            log('Core initialization...');

            core.pre();

            __b = $('body');
            __h = $('html');
            __hb = $('html,body');
            __mobile = false;
            __touch = Modernizr.touch;

            _theme = __b.find('> .theme');

            _nav = _theme.find('> .navigation');
            _header = _theme.find('> .header');
            _content = _theme.find('> .content');
            _footer = _theme.find('> .footer');

            // device detection
            if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
                    || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0, 4)))
                __mobile = true;

            if (_w.ga) {
                log('GoogleAnalytics is available.');
            }

            $.each(core.once, function () {
                if (typeof this === 'function') {
                    this.call(core);
                }
            });

            $.each(__b.prop('class').split(' '), function () {
                if (core.autoBody.hasOwnProperty(this)) {
                    core.autoBody[this].call(core);
                }
            });//*/

            core.execAuto();

            log('Core pre function\'s execution done.');

            log('Core initialization done.');

            //__b.removeClass('js-preload');

            core.imageLoader();
        },
        pre: function () {
            if (!String.prototype.format) {
                String.prototype.format = function () {
                    var args = arguments;
                    return this.replace(/{(\d+)}/g, function (match, number) {
                        return typeof args[number] != 'undefined' ? args[number] : match;
                    });
                };
            }
            if (!String.prototype.toInt) {
                String.prototype.toInt = function () {
                    return parseInt(this);
                };
            }
            if (!String.prototype.parseURL) {
                String.prototype.parseURL = function (component) {
                    //       discuss at: http://phpjs.org/functions/parse_url/
                    //      original by: Steven Levithan (http://blog.stevenlevithan.com)
                    // reimplemented by: Brett Zamir (http://brett-zamir.me)
                    //         input by: Lorenzo Pisani
                    //         input by: Tony
                    //      improved by: Brett Zamir (http://brett-zamir.me)
                    //             note: original by http://stevenlevithan.com/demo/parseuri/js/assets/parseuri.js
                    //             note: blog post at http://blog.stevenlevithan.com/archives/parseuri
                    //             note: demo at http://stevenlevithan.com/demo/parseuri/js/assets/parseuri.js
                    //             note: Does not replace invalid characters with '_' as in PHP, nor does it return false with
                    //             note: a seriously malformed URL.
                    //             note: Besides function name, is essentially the same as parseUri as well as our allowing
                    //             note: an extra slash after the scheme/protocol (to allow file:/// as in PHP)
                    //        example 1: parse_url('http://username:password@hostname/path?arg=value#anchor');
                    //        returns 1: {scheme: 'http', host: 'hostname', user: 'username', pass: 'password', path: '/path', query: 'arg=value', fragment: 'anchor'}

                    var query, key = ['source', 'scheme', 'authority', 'userInfo', 'user', 'pass', 'host', 'port',
                        'relative', 'path', 'directory', 'file', 'query', 'fragment'
                    ],
                            ini = {},
                            mode = 'php',
                            parser = {
                                php: /^(?:([^:\/?#]+):)?(?:\/\/()(?:(?:()(?:([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?))?()(?:(()(?:(?:[^?#\/]*\/)*)()(?:[^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
                                strict: /^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
                                loose: /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/\/?)?((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/ // Added one optional slash to post-scheme to catch file:/// (should restrict this)
                            };

                    var m = parser[mode].exec(this),
                            uri = {},
                            i = 14;
                    while (i--) {
                        if (m[i]) {
                            uri[key[i]] = m[i];
                        }
                    }

                    if (component) {
                        return uri[component.replace('PHP_URL_', '')
                                .toLowerCase()];
                    }
                    delete uri.source;
                    return uri;

                }
            }

            if (!$.fn.sleep) {
                $.fn.sleep = function (timeout, func) {
                    var $this = $(this);
                    var functions = $this.data('timeouts') || [];

                    functions.push({
                        func: func,
                        timeout: timeout
                    });

                    $this.data('timeouts', functions);

                    return this;
                };
            }

            if (!$.fn.sleepExec) {
                $.fn.sleepExec = function () {
                    var $this = $(this);
                    var list = $this.data('timeouts') || [];
                    var item = list.shift();

                    if (typeof item === 'object') {

                        $this.data('timeouts', list);

                        setTimeout(function () {
                            item.func();
                            if (list.length > 0) {
                                $this.sleepExec();
                            }
                        }, item.timeout);
                    }
                };
            }
        },
        imageLoader: function () {
            var loaded = [], images;

            images = __b.find('*').map(function () {
                var el = $(this), url = false;

                if (el.css('background-image') !== 'none') {
                    url = /url\(['"]?([^'"]+)['"]?\)/.exec(el.css('background-image'))[1];
                } else
                if (el.prop('tagName') === 'IMG' && el.attr('src')) {
                    url = el.attr('src');
                }

                if (url) {
                    return url;
                }
            }).toArray();

            $.each(images, function () {
                var url = this, img = $('<img class="invisible" />');

                __b.append(img);

                img.load(function () {
                    loaded.push(url);

                    img.remove();

                    if (loaded.length === images.length) {
                        __h.removeClass('js-preload js-preload-transition');
                    }
                }).attr('src', url);
            });
        }
    };

    $(function () {
        core.init.call(core);
    });
})(window, console, jQuery);