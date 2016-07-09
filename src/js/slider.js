/* 
 *  @license © 2014, Amin Paks, T. (514) 441-2413, W. http://www.aminpaks.com
 */
(function (_w, _c, $) {
    'use strict';

    var
            __w = $(_w),
            __b = $('body'),
            __h = $('html'),
            __hb = $('html,body');

    var log = function () {
        if (_c && _c.log) {
            _c.log.apply(_c, arguments);
        }
    };

    var slider_obj = _w._slider_handler = function (dom, settings) {
        return new slider_obj.fn.init(dom, settings);
    };
    slider_obj.fn = slider_obj.prototype = {
        self: function () {
            return $(this._self);
        },
        window: function () {
            return $(this._w);
        },
        slide: function (idx) {
            return this.slides().eq(idx);
        },
        slideIdx: function () {
            return this._activeSlideIdx || 0;
        },
        slides: function () {
            return this.self().find('.slide');
        },
        slidesSync: function () {
            this.slidesWrapper().css('left', this.width() * this.slideIdx() * -1);
        },
        slidesTitle: function () {
            return this.self().find('.slide-title');
        },
        slidesWrapper: function () {
            return this.self().find('.slides-wrapper');
        },
        slidesSetBackImage: function () {
            this.slides().each(function () {
                var slide = $(this);

                if (slide.attr('data-image')) {
                    slide.css('background-image', 'url("' + slide.attr('data-image') + '")');
                }
            });
        },
        thumb: {
            thumbMargin: 5,
            thumbPerView: 8,
            holder: function () {
                return this.owner().find('.thumbnails-holder');
            },
            thumbIdx: function () {
                return this.ownerObj().slideIdx();
            },
            holderThumbIdx: function () {
                var idx = this._holderThumbIdx || 0;

                if (typeof arguments[0] === 'number' && !isNaN(arguments[0])) {
                    var newIdx = arguments[0];

                    if (newIdx !== idx) {
                        return this._holderThumbIdx = newIdx;
                    }
                }

                return idx;
            },
            holderLeft: function () {
                return this.holderThumbIdx() * this.thumbWidth(true) * -1;
            },
            holderLeftMax: function () {
                var
                        perView = this.thumbPerView,
                        thumbWidth = this.thumbWidth(true),
                        holderWidth = this.holderWidth(),
                        holderViewWidth = perView * thumbWidth,
                        holderLeftMax = holderWidth - holderViewWidth;

                if (holderLeftMax < 0) {
                    return 0;
                }

                return holderLeftMax;
            },
            holderWidth: function () {
                var
                        thumbWidth = this.thumbWidth(true),
                        thumbCount = this.controlObj().list().size();

                return thumbWidth * thumbCount;
            },
            holderSlipCount: function () {
                var dir = 0;

                if (typeof arguments[0] === 'number' && !isNaN(arguments[0])) {
                    dir = arguments[0];
                }

                var
                        perView = this.thumbPerView,
                        count = Math.round(perView / 4),
                        hThbIdx = this.holderThumbIdx(),
                        thbIdx = this.thumbIdx(),
                        midCount = Math.round(perView / 2);

                if (dir < 0) {
                    if (hThbIdx + midCount < thbIdx) {
                        return count;
                    }
                }
                else
                if (dir > 0) {
                    if (hThbIdx + midCount - 1 > thbIdx) {
                        return count;
                    }
                }

                return 1;
            },
            holderSlip: function (dir) {
                var
                        curLeft = this.holderLeft(),
                        thumbWidth = this.thumbWidth(true),
                        slipCnt = this.holderSlipCount(dir),
                        maxLeft = 0,
                        minLeft = this.holderLeftMax() * -1,
                        plus = thumbWidth * slipCnt * dir,
                        newLeft = curLeft + plus;

                if (typeof dir === 'undefined') {
                    return false;
                }

                if (newLeft > maxLeft) {
                    newLeft = maxLeft;
                } else
                if (newLeft < minLeft) {
                    newLeft = minLeft;
                }

                var newThumbIdx = Math.abs(newLeft) / thumbWidth;

                this.holderThumbIdx(newThumbIdx);

                this.holderPageSync(true);

                return true;
            },
            holderPageSync: function (animate) {
                var left = this.holderThumbIdx() * this.thumbWidth(true) * -1;
                if (animate === true) {
                    this.holder().stop().animate({
                        left: left
                    }, 400);
                } else {
                    this.holder().css('left', left);
                }
            },
            wrapper: function () {
                return this.owner().find('.thumbnails-wrapper');
            },
            owner: function () {
                return this._owner.self();
            },
            ownerObj: function () {
                return this._owner;
            },
            control: function () {
                return this._control.self();
            },
            controlObj: function () {
                return this._control;
            },
            btn: function (name) {
                return this.control().find('{0}'.format(name));
            },
            btnsInit: function () {
                var _this = this;
                this.btn('.prev,.next').on('click', function (event) {
                    event.preventDefault();

                    _this.btnsOnClick.call(_this, event, $(this).hasClass('next') ? -1 : 1);

                    return false;
                });
            },
            btnsOnClick: function (event, dir) {
                this.holderSlip(dir);
            },
            thumbWidth: function (plusMargin) {
                var
                        _this = this,
                        thumbPerView = _this.thumbPerView,
                        thumbMargin = _this.thumbMargin,
                        wrapWidth = _this.wrapper().width(),
                        thumbWidth = (wrapWidth - (thumbPerView - 1) * thumbMargin) / thumbPerView;

                if (plusMargin === true) {
                    plusMargin = thumbMargin;
                } else {
                    plusMargin = 0;
                }

                return Math.round(thumbWidth) + plusMargin;
            },
            init: function () {
                var
                        slider = this,
                        sliderControl = slider.control,
                        sliderThumb = slider.thumb;

                sliderThumb._owner = slider;
                sliderThumb._control = sliderControl;
                sliderThumb._self = sliderThumb;
                if (sliderControl.hasThumbnail()) {
                    sliderThumb.btnsInit.call(sliderThumb);
                    sliderThumb.holderPageSync();
                }

                return sliderThumb;
            }
        },
        control: {
            self: function () {
                return this.owner().find('.slider-control');
            },
            selfObj: function () {
                return this._self;
            },
            owner: function () {
                return this._owner.self();
            },
            ownerObj: function () {
                return this._owner;
            },
            list: function () {
                return this.self().find('li');
            },
            listItem: function (idx) {
                return this.list().eq(idx);
            },
            listItemActive: function (idx) {
                this.btn('.prev,.next').removeClass('disabled');
                if (idx <= 0) {
                    this.btn('.prev').addClass('disabled');
                } else
                if (idx >= this.ownerObj().slides().size() - 1) {
                    this.btn('.next').addClass('disabled');
                }
                this.list().removeClass('active');
                this.listItem(idx).addClass('active');
            },
            onItemClick: function (event, idx) {
                this._activeIdx = idx;
                this.ownerObj().setSlideIndex(idx, true);
            },
            btn: function (name) {
                return this.self().find(name);
            },
            btnsInit: function () {
                var _this = this;
                var btns = this.btn('.prev,.next');

                btns.each(function () {
                    var el = null;

                    if (this.tagName !== 'a') {
                        el = $(this).find('> a');
                    } else {
                        el = $(this);
                    }

                    if (el.attr('href') === undefined) {
                        el.attr('href', 'javascript:void(0)');
                    }
                });

                btns.on('click', function (event) {
                    event.preventDefault();

                    _this.btnsOnClick.call(_this, event, $(this).hasClass('next') ? -1 : 1, this);

                    return false;
                });
            },
            btnsOnClick: function (event, dir, dom) {
                this.ownerObj().setSlidePage(dir, true);
            },
            listInit: function () {
                var _this = this;
                this.list().each(function (idx) {
                    var
                            li = $(this),
                            link = li.find('> a');

                    link.on('click', function (event) {
                        event.preventDefault();
                        _this.onItemClick.call(_this, event, idx)
                        return false;
                    });
                    if (li.attr('data-thumb') && link.css('background-image') === 'none') {
                        link.css('background-image', 'url("{0}")'.format(li.attr('data-thumb')));
                    }
                });

                _this.btnsInit();
            },
            init: function () {
                var slider = this,
                        sliderControl = slider.control;

                sliderControl._owner = slider;
                sliderControl._self = sliderControl;
                sliderControl.listInit.call(sliderControl);

                return sliderControl;
            },
            hasThumbnail: function () {
                return this.self().hasClass('thumbnails');
            }
        },
        width: function () {
            return this.self().innerWidth();
        },
        parent: function () {
            return this.self.parent();
        },
        exec: function () {
            var _this = this;

            this.slidesSetBackImage();

            // prepare controls
            this.control.init.call(this);
            this.thumb.init.call(this);

            // The window resize listener
            this.window().on('resize', function () {
                _this.onWindowResize.call(_this);
            });

            // show the first slide
            this.setSlideIndex();

            // rearrange the elements' width for the first exec
            this.onWindowResize.call(_this);

            if (this.settings.autoRotate) {
                this.rotatorID = setInterval(function () {

                    var
                            idx = _this.slideIdx(),
                            max = _this.control.list().size();

                    idx++;

                    if (idx >= max) {
                        idx = 0;
                    }

                    _this.setSlideIndex(idx, true);

                }, this.settings.autoRotateDelay);
            }

            return this;
        },
        setSlideTitle: function (idx) {
            var
                    text = this.slide(idx).attr('data-title'),
                    slideText = this.slidesTitle().find('.slide-text'),
                    slidePage = this.slidesTitle().find('.slide-page');

            if (text) {
                slideText.text(text);
                slidePage.text(slidePage.attr('data-format').format(idx + 1, this.control.list().size()));
            }
        },
        setSlideIndex: function (idx, animate, callback) {
            var
                    _this = this;

            idx = (idx && parseInt(idx)) || 0;

            _this._activeSlideIdx = idx;

            if (animate) {
                this.animateSlide(idx, function () {
                    _this.setSlideTitle(idx);
                    _this.control.listItemActive(idx);
                    if (typeof callback === 'function') {
                        callback.call(_this, idx);
                    }
                });
            } else {
                this.slidesWrapper().css('left', this.width() * idx * -1);
                this.setSlideTitle(idx);
                this.control.listItemActive(idx);
                if (typeof callback === 'function') {
                    callback.call(_this, idx);
                }
            }
        },
        setSlidePage: function (dir, animate, callback) {
            var _this = this;

            var newIdx = (_this.slideIdx() - dir);

            newIdx = Math.min(Math.max(newIdx, 0), _this.slides().size() - 1);

            _this.setSlideIndex(newIdx, animate, callback);
        },
        animateSlide: function (idx, callback) {
            var _this = this;
            this.slidesWrapper().stop().animate({
                left: this.width() * idx * -1
            }, _this.settings.animateSpeed, function () {
                if (typeof callback === 'function') {
                    callback.call(_this, idx);
                }
            });
        },
        onWindowResize: function () {
            var width = this.width(),
                    slides = this.slides(),
                    slidesWrapper = this.slidesWrapper(),
                    control = this.control,
                    thumb = this.thumb;

            slides.css({
                'width': width,
                'height': slidesWrapper.innerHeight(),
            });
            slidesWrapper.css({
                left: this.slideIdx() * width * -1,
                width: width * this.slides().size()
            });

            if (control.hasThumbnail()) {

                // set the thumbnails <li> width & height
                control.list().css({
                    width: thumb.thumbWidth(),
                    //height: thumbWidth * .56
                });

                // set the .thumbnail-holder width
                thumb.holder().css({
                    width: thumb.holderWidth()
                });

                thumb.wrapper().height(thumb.holder().outerHeight());

                thumb.holderPageSync();
            }
        }
    };
    var slider_obj_init = slider_obj.fn.init = function (dom, settings) {

        this.settings = $.extend({
            animateSpeed: 500,
            autoRotate: false,
            autoRotateDelay: 1000,
        }, settings);

        this._self = dom;
        this._w = _w;
        this.exec.apply(this);

        return this;
    };
    slider_obj_init.prototype = slider_obj.fn;

})(window, console, jQuery);