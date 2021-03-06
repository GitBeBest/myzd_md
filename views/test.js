/*!
 * iScroll v4.2.5 ~ Copyright (c) 2012 Matteo Spinelli, http://cubiq.org
 * Released under MIT license, http://cubiq.org/license
 */
(function (i, E) {
    var u = Math, n = E.createElement("div").style, z = (function () {
        var H = "t,webkitT,MozT,msT,OT".split(","), G, F = 0, m = H.length;
        for (; F < m; F++) {
            G = H[F] + "ransform";
            if (G in n) {
                return H[F].substr(0, H[F].length - 1)
            }
        }
        return false
    })(), D = z ? "-" + z.toLowerCase() + "-" : "", l = s("transform"), x = s("transitionProperty"), k = s("transitionDuration"), o = s("transformOrigin"), B = s("transitionTimingFunction"), e = s("transitionDelay"), A = (/android/gi).test(navigator.appVersion), h = (/iphone|ipad/gi).test(navigator.appVersion), r = (/hp-tablet/gi).test(navigator.appVersion), j = s("perspective") in n, y = "ontouchstart" in i && !r, d = z !== false, f = s("transition") in n, g = "onorientationchange" in i ? "orientationchange" : "resize", b = y ? "touchstart" : "mousedown", t = y ? "touchmove" : "mousemove", c = y ? "touchend" : "mouseup", w = y ? "touchcancel" : "mouseup", a = (function () {
        if (z === false) {
            return false
        }
        var m = {
            "": "transitionend",
            "webkit": "webkitTransitionEnd",
            "Moz": "transitionend",
            "O": "otransitionend",
            "ms": "MSTransitionEnd"
        };
        return m[z]
    })(), q = (function () {
        return i.requestAnimationFrame || i.webkitRequestAnimationFrame || i.mozRequestAnimationFrame || i.oRequestAnimationFrame || i.msRequestAnimationFrame || function (m) {
                return setTimeout(m, 1)
            }
    })(), p = (function () {
        return i.cancelRequestAnimationFrame || i.webkitCancelAnimationFrame || i.webkitCancelRequestAnimationFrame || i.mozCancelRequestAnimationFrame || i.oCancelRequestAnimationFrame || i.msCancelRequestAnimationFrame || clearTimeout
    })(), C = j ? " translateZ(0)" : "", v = function (G, m) {
        var H = this, F;
        H.wrapper = typeof G == "object" ? G : E.getElementById(G);
        H.wrapper.style.overflow = "hidden";
        H.scroller = H.wrapper.children[0];
        H.options = {
            hScroll: true,
            vScroll: true,
            x: 0,
            y: 0,
            bounce: true,
            bounceLock: false,
            momentum: true,
            lockDirection: true,
            useTransform: true,
            useTransition: false,
            topOffset: 0,
            checkDOMChanges: false,
            handleClick: true,
            hScrollbar: true,
            vScrollbar: true,
            fixedScrollbar: A,
            hideScrollbar: h,
            fadeScrollbar: h && j,
            scrollbarClass: "",
            zoom: false,
            zoomMin: 1,
            zoomMax: 4,
            doubleTapZoom: 2,
            wheelAction: "scroll",
            snap: false,
            snapThreshold: 1,
            onRefresh: null,
            onBeforeScrollStart: function (I) {
                I.preventDefault()
            },
            onScrollStart: null,
            onBeforeScrollMove: null,
            onScrollMove: null,
            onBeforeScrollEnd: null,
            onScrollEnd: null,
            onTouchEnd: null,
            onDestroy: null,
            onZoomStart: null,
            onZoom: null,
            onZoomEnd: null
        };
        for (F in m) {
            H.options[F] = m[F]
        }
        H.x = H.options.x;
        H.y = H.options.y;
        H.options.useTransform = d && H.options.useTransform;
        H.options.hScrollbar = H.options.hScroll && H.options.hScrollbar;
        H.options.vScrollbar = H.options.vScroll && H.options.vScrollbar;
        H.options.zoom = H.options.useTransform && H.options.zoom;
        H.options.useTransition = f && H.options.useTransition;
        if (H.options.zoom && A) {
            C = ""
        }
        H.scroller.style[x] = H.options.useTransform ? D + "transform" : "top left";
        H.scroller.style[k] = "0";
        H.scroller.style[o] = "0 0";
        if (H.options.useTransition) {
            H.scroller.style[B] = "cubic-bezier(0.33,0.66,0.66,1)"
        }
        if (H.options.useTransform) {
            H.scroller.style[l] = "translate(" + H.x + "px," + H.y + "px)" + C
        } else {
            H.scroller.style.cssText += ";position:absolute;top:" + H.y + "px;left:" + H.x + "px"
        }
        if (H.options.useTransition) {
            H.options.fixedScrollbar = true
        }
        H.refresh();
        H._bind(g, i);
        H._bind(b);
        if (!y) {
            if (H.options.wheelAction != "none") {
                H._bind("DOMMouseScroll");
                H._bind("mousewheel")
            }
        }
        if (H.options.checkDOMChanges) {
            H.checkDOMTime = setInterval(function () {
                H._checkDOMChanges()
            }, 500)
        }
    };
    v.prototype = {
        enabled: true,
        x: 0,
        y: 0,
        steps: [],
        scale: 1,
        currPageX: 0,
        currPageY: 0,
        pagesX: [],
        pagesY: [],
        aniTime: null,
        wheelZoomCount: 0,
        handleEvent: function (F) {
            var m = this;
            switch (F.type) {
                case b:
                    if (!y && F.button !== 0) {
                        return
                    }
                    m._start(F);
                    break;
                case t:
                    m._move(F);
                    break;
                case c:
                case w:
                    m._end(F);
                    break;
                case g:
                    m._resize();
                    break;
                case"DOMMouseScroll":
                case"mousewheel":
                    m._wheel(F);
                    break;
                case a:
                    m._transitionEnd(F);
                    break
            }
        },
        _checkDOMChanges: function () {
            if (this.moved || this.zoomed || this.animating || (this.scrollerW == this.scroller.offsetWidth * this.scale && this.scrollerH == this.scroller.offsetHeight * this.scale)) {
                return
            }
            this.refresh()
        },
        _scrollbar: function (m) {
            var G = this, F;
            if (!G[m + "Scrollbar"]) {
                if (G[m + "ScrollbarWrapper"]) {
                    if (d) {
                        G[m + "ScrollbarIndicator"].style[l] = ""
                    }
                    G[m + "ScrollbarWrapper"].parentNode.removeChild(G[m + "ScrollbarWrapper"]);
                    G[m + "ScrollbarWrapper"] = null;
                    G[m + "ScrollbarIndicator"] = null
                }
                return
            }
            if (!G[m + "ScrollbarWrapper"]) {
                F = E.createElement("div");
                if (G.options.scrollbarClass) {
                    F.className = G.options.scrollbarClass + m.toUpperCase()
                } else {
                    F.style.cssText = "position:absolute;z-index:100;" + (m == "h" ? "height:7px;bottom:1px;left:2px;right:" + (G.vScrollbar ? "7" : "2") + "px" : "width:7px;bottom:" + (G.hScrollbar ? "7" : "2") + "px;top:2px;right:1px")
                }
                F.style.cssText += ";pointer-events:none;" + D + "transition-property:opacity;" + D + "transition-duration:" + (G.options.fadeScrollbar ? "350ms" : "0") + ";overflow:hidden;opacity:" + (G.options.hideScrollbar ? "0" : "1");
                G.wrapper.appendChild(F);
                G[m + "ScrollbarWrapper"] = F;
                F = E.createElement("div");
                if (!G.options.scrollbarClass) {
                    F.style.cssText = "position:absolute;z-index:100;background:rgba(0,0,0,0.5);border:1px solid rgba(255,255,255,0.9);" + D + "background-clip:padding-box;" + D + "box-sizing:border-box;" + (m == "h" ? "height:100%" : "width:100%") + ";" + D + "border-radius:3px;border-radius:3px"
                }
                F.style.cssText += ";pointer-events:none;" + D + "transition-property:" + D + "transform;" + D + "transition-timing-function:cubic-bezier(0.33,0.66,0.66,1);" + D + "transition-duration:0;" + D + "transform: translate(0,0)" + C;
                if (G.options.useTransition) {
                    F.style.cssText += ";" + D + "transition-timing-function:cubic-bezier(0.33,0.66,0.66,1)"
                }
                G[m + "ScrollbarWrapper"].appendChild(F);
                G[m + "ScrollbarIndicator"] = F
            }
            if (m == "h") {
                G.hScrollbarSize = G.hScrollbarWrapper.clientWidth;
                G.hScrollbarIndicatorSize = u.max(u.round(G.hScrollbarSize * G.hScrollbarSize / G.scrollerW), 8);
                G.hScrollbarIndicator.style.width = G.hScrollbarIndicatorSize + "px";
                G.hScrollbarMaxScroll = G.hScrollbarSize - G.hScrollbarIndicatorSize;
                G.hScrollbarProp = G.hScrollbarMaxScroll / G.maxScrollX
            } else {
                G.vScrollbarSize = G.vScrollbarWrapper.clientHeight;
                G.vScrollbarIndicatorSize = u.max(u.round(G.vScrollbarSize * G.vScrollbarSize / G.scrollerH), 8);
                G.vScrollbarIndicator.style.height = G.vScrollbarIndicatorSize + "px";
                G.vScrollbarMaxScroll = G.vScrollbarSize - G.vScrollbarIndicatorSize;
                G.vScrollbarProp = G.vScrollbarMaxScroll / G.maxScrollY
            }
            G._scrollbarPos(m, true)
        },
        _resize: function () {
            var m = this;
            setTimeout(function () {
                m.refresh()
            }, A ? 200 : 0)
        },
        _pos: function (m, F) {
            if (this.zoomed) {
                return
            }
            m = this.hScroll ? m : 0;
            F = this.vScroll ? F : 0;
            if (this.options.useTransform) {
                this.scroller.style[l] = "translate(" + m + "px," + F + "px) scale(" + this.scale + ")" + C
            } else {
                m = u.round(m);
                F = u.round(F);
                this.scroller.style.left = m + "px";
                this.scroller.style.top = F + "px"
            }
            this.x = m;
            this.y = F;
            this._scrollbarPos("h");
            this._scrollbarPos("v")
        },
        _scrollbarPos: function (m, H) {
            var G = this, I = m == "h" ? G.x : G.y, F;
            if (!G[m + "Scrollbar"]) {
                return
            }
            I = G[m + "ScrollbarProp"] * I;
            if (I < 0) {
                if (!G.options.fixedScrollbar) {
                    F = G[m + "ScrollbarIndicatorSize"] + u.round(I * 3);
                    if (F < 8) {
                        F = 8
                    }
                    G[m + "ScrollbarIndicator"].style[m == "h" ? "width" : "height"] = F + "px"
                }
                I = 0
            } else {
                if (I > G[m + "ScrollbarMaxScroll"]) {
                    if (!G.options.fixedScrollbar) {
                        F = G[m + "ScrollbarIndicatorSize"] - u.round((I - G[m + "ScrollbarMaxScroll"]) * 3);
                        if (F < 8) {
                            F = 8
                        }
                        G[m + "ScrollbarIndicator"].style[m == "h" ? "width" : "height"] = F + "px";
                        I = G[m + "ScrollbarMaxScroll"] + (G[m + "ScrollbarIndicatorSize"] - F)
                    } else {
                        I = G[m + "ScrollbarMaxScroll"]
                    }
                }
            }
            G[m + "ScrollbarWrapper"].style[e] = "0";
            G[m + "ScrollbarWrapper"].style.opacity = H && G.options.hideScrollbar ? "0" : "1";
            G[m + "ScrollbarIndicator"].style[l] = "translate(" + (m == "h" ? I + "px,0)" : "0," + I + "px)") + C
        },
        _start: function (K) {
            var J = this, F = y ? K.touches[0] : K, G, m, L, I, H;
            if (!J.enabled) {
                return
            }
            if (J.options.onBeforeScrollStart) {
                J.options.onBeforeScrollStart.call(J, K)
            }
            if (J.options.useTransition || J.options.zoom) {
                J._transitionTime(0)
            }
            J.moved = false;
            J.animating = false;
            J.zoomed = false;
            J.distX = 0;
            J.distY = 0;
            J.absDistX = 0;
            J.absDistY = 0;
            J.dirX = 0;
            J.dirY = 0;
            if (J.options.zoom && y && K.touches.length > 1) {
                I = u.abs(K.touches[0].pageX - K.touches[1].pageX);
                H = u.abs(K.touches[0].pageY - K.touches[1].pageY);
                J.touchesDistStart = u.sqrt(I * I + H * H);
                J.originX = u.abs(K.touches[0].pageX + K.touches[1].pageX - J.wrapperOffsetLeft * 2) / 2 - J.x;
                J.originY = u.abs(K.touches[0].pageY + K.touches[1].pageY - J.wrapperOffsetTop * 2) / 2 - J.y;
                if (J.options.onZoomStart) {
                    J.options.onZoomStart.call(J, K)
                }
            }
            if (J.options.momentum) {
                if (J.options.useTransform) {
                    G = getComputedStyle(J.scroller, null)[l].replace(/[^0-9\-.,]/g, "").split(",");
                    m = +(G[12] || G[4]);
                    L = +(G[13] || G[5])
                } else {
                    m = +getComputedStyle(J.scroller, null).left.replace(/[^0-9-]/g, "");
                    L = +getComputedStyle(J.scroller, null).top.replace(/[^0-9-]/g, "")
                }
                if (m != J.x || L != J.y) {
                    if (J.options.useTransition) {
                        J._unbind(a)
                    } else {
                        p(J.aniTime)
                    }
                    J.steps = [];
                    J._pos(m, L);
                    if (J.options.onScrollEnd) {
                        J.options.onScrollEnd.call(J)
                    }
                }
            }
            J.absStartX = J.x;
            J.absStartY = J.y;
            J.startX = J.x;
            J.startY = J.y;
            J.pointX = F.pageX;
            J.pointY = F.pageY;
            J.startTime = K.timeStamp || Date.now();
            if (J.options.onScrollStart) {
                J.options.onScrollStart.call(J, K)
            }
            J._bind(t, i);
            J._bind(c, i);
            J._bind(w, i)
        },
        _move: function (M) {
            var K = this, N = y ? M.touches[0] : M, I = N.pageX - K.pointX, G = N.pageY - K.pointY, m = K.x + I, O = K.y + G, J, H, F, L = M.timeStamp || Date.now();
            if (K.options.onBeforeScrollMove) {
                K.options.onBeforeScrollMove.call(K, M)
            }
            if (K.options.zoom && y && M.touches.length > 1) {
                J = u.abs(M.touches[0].pageX - M.touches[1].pageX);
                H = u.abs(M.touches[0].pageY - M.touches[1].pageY);
                K.touchesDist = u.sqrt(J * J + H * H);
                K.zoomed = true;
                F = 1 / K.touchesDistStart * K.touchesDist * this.scale;
                if (F < K.options.zoomMin) {
                    F = 0.5 * K.options.zoomMin * Math.pow(2, F / K.options.zoomMin)
                } else {
                    if (F > K.options.zoomMax) {
                        F = 2 * K.options.zoomMax * Math.pow(0.5, K.options.zoomMax / F)
                    }
                }
                K.lastScale = F / this.scale;
                m = this.originX - this.originX * K.lastScale + this.x;
                O = this.originY - this.originY * K.lastScale + this.y;
                this.scroller.style[l] = "translate(" + m + "px," + O + "px) scale(" + F + ")" + C;
                if (K.options.onZoom) {
                    K.options.onZoom.call(K, M)
                }
                return
            }
            K.pointX = N.pageX;
            K.pointY = N.pageY;
            if (m > 0 || m < K.maxScrollX) {
                m = K.options.bounce ? K.x + (I / 2) : m >= 0 || K.maxScrollX >= 0 ? 0 : K.maxScrollX
            }
            if (O > K.minScrollY || O < K.maxScrollY) {
                O = K.options.bounce ? K.y + (G / 2) : O >= K.minScrollY || K.maxScrollY >= 0 ? K.minScrollY : K.maxScrollY
            }
            K.distX += I;
            K.distY += G;
            K.absDistX = u.abs(K.distX);
            K.absDistY = u.abs(K.distY);
            if (K.absDistX < 6 && K.absDistY < 6) {
                return
            }
            if (K.options.lockDirection) {
                if (K.absDistX > K.absDistY + 5) {
                    O = K.y;
                    G = 0
                } else {
                    if (K.absDistY > K.absDistX + 5) {
                        m = K.x;
                        I = 0
                    }
                }
            }
            K.moved = true;
            K._pos(m, O);
            K.dirX = I > 0 ? -1 : I < 0 ? 1 : 0;
            K.dirY = G > 0 ? -1 : G < 0 ? 1 : 0;
            if (L - K.startTime > 300) {
                K.startTime = L;
                K.startX = K.x;
                K.startY = K.y
            }
            if (K.options.onScrollMove) {
                K.options.onScrollMove.call(K, M)
            }
        },
        _end: function (M) {
            if (y && M.touches.length !== 0) {
                return
            }
            var K = this, S = y ? M.changedTouches[0] : M, N, R, G = {dist: 0, time: 0}, m = {
                dist: 0,
                time: 0
            }, J = (M.timeStamp || Date.now()) - K.startTime, O = K.x, L = K.y, Q, P, F, I, H;
            K._unbind(t, i);
            K._unbind(c, i);
            K._unbind(w, i);
            if (K.options.onBeforeScrollEnd) {
                K.options.onBeforeScrollEnd.call(K, M)
            }
            if (K.zoomed) {
                H = K.scale * K.lastScale;
                H = Math.max(K.options.zoomMin, H);
                H = Math.min(K.options.zoomMax, H);
                K.lastScale = H / K.scale;
                K.scale = H;
                K.x = K.originX - K.originX * K.lastScale + K.x;
                K.y = K.originY - K.originY * K.lastScale + K.y;
                K.scroller.style[k] = "200ms";
                K.scroller.style[l] = "translate(" + K.x + "px," + K.y + "px) scale(" + K.scale + ")" + C;
                K.zoomed = false;
                K.refresh();
                if (K.options.onZoomEnd) {
                    K.options.onZoomEnd.call(K, M)
                }
                return
            }
            if (!K.moved) {
                if (y) {
                    if (K.doubleTapTimer && K.options.zoom) {
                        clearTimeout(K.doubleTapTimer);
                        K.doubleTapTimer = null;
                        if (K.options.onZoomStart) {
                            K.options.onZoomStart.call(K, M)
                        }
                        K.zoom(K.pointX, K.pointY, K.scale == 1 ? K.options.doubleTapZoom : 1);
                        if (K.options.onZoomEnd) {
                            setTimeout(function () {
                                K.options.onZoomEnd.call(K, M)
                            }, 200)
                        }
                    } else {
                        if (this.options.handleClick) {
                            K.doubleTapTimer = setTimeout(function () {
                                K.doubleTapTimer = null;
                                N = S.target;
                                while (N.nodeType != 1) {
                                    N = N.parentNode
                                }
                                if (N.tagName != "SELECT" && N.tagName != "INPUT" && N.tagName != "TEXTAREA") {
                                    R = E.createEvent("MouseEvents");
                                    R.initMouseEvent("click", true, true, M.view, 1, S.screenX, S.screenY, S.clientX, S.clientY, M.ctrlKey, M.altKey, M.shiftKey, M.metaKey, 0, null);
                                    R._fake = true;
                                    N.dispatchEvent(R)
                                }
                            }, K.options.zoom ? 250 : 0)
                        }
                    }
                }
                K._resetPos(400);
                if (K.options.onTouchEnd) {
                    K.options.onTouchEnd.call(K, M)
                }
                return
            }
            if (J < 300 && K.options.momentum) {
                G = O ? K._momentum(O - K.startX, J, -K.x, K.scrollerW - K.wrapperW + K.x, K.options.bounce ? K.wrapperW : 0) : G;
                m = L ? K._momentum(L - K.startY, J, -K.y, (K.maxScrollY < 0 ? K.scrollerH - K.wrapperH + K.y - K.minScrollY : 0), K.options.bounce ? K.wrapperH : 0) : m;
                O = K.x + G.dist;
                L = K.y + m.dist;
                if ((K.x > 0 && O > 0) || (K.x < K.maxScrollX && O < K.maxScrollX)) {
                    G = {dist: 0, time: 0}
                }
                if ((K.y > K.minScrollY && L > K.minScrollY) || (K.y < K.maxScrollY && L < K.maxScrollY)) {
                    m = {dist: 0, time: 0}
                }
            }
            if (G.dist || m.dist) {
                F = u.max(u.max(G.time, m.time), 10);
                if (K.options.snap) {
                    Q = O - K.absStartX;
                    P = L - K.absStartY;
                    if (u.abs(Q) < K.options.snapThreshold && u.abs(P) < K.options.snapThreshold) {
                        K.scrollTo(K.absStartX, K.absStartY, 200)
                    } else {
                        I = K._snap(O, L);
                        O = I.x;
                        L = I.y;
                        F = u.max(I.time, F)
                    }
                }
                K.scrollTo(u.round(O), u.round(L), F);
                if (K.options.onTouchEnd) {
                    K.options.onTouchEnd.call(K, M)
                }
                return
            }
            if (K.options.snap) {
                Q = O - K.absStartX;
                P = L - K.absStartY;
                if (u.abs(Q) < K.options.snapThreshold && u.abs(P) < K.options.snapThreshold) {
                    K.scrollTo(K.absStartX, K.absStartY, 200)
                } else {
                    I = K._snap(K.x, K.y);
                    if (I.x != K.x || I.y != K.y) {
                        K.scrollTo(I.x, I.y, I.time)
                    }
                }
                if (K.options.onTouchEnd) {
                    K.options.onTouchEnd.call(K, M)
                }
                return
            }
            K._resetPos(200);
            if (K.options.onTouchEnd) {
                K.options.onTouchEnd.call(K, M)
            }
        },
        _resetPos: function (G) {
            var m = this, H = m.x >= 0 ? 0 : m.x < m.maxScrollX ? m.maxScrollX : m.x, F = m.y >= m.minScrollY || m.maxScrollY > 0 ? m.minScrollY : m.y < m.maxScrollY ? m.maxScrollY : m.y;
            if (H == m.x && F == m.y) {
                if (m.moved) {
                    m.moved = false;
                    if (m.options.onScrollEnd) {
                        m.options.onScrollEnd.call(m)
                    }
                }
                if (m.hScrollbar && m.options.hideScrollbar) {
                    if (z == "webkit") {
                        m.hScrollbarWrapper.style[e] = "300ms"
                    }
                    m.hScrollbarWrapper.style.opacity = "0"
                }
                if (m.vScrollbar && m.options.hideScrollbar) {
                    if (z == "webkit") {
                        m.vScrollbarWrapper.style[e] = "300ms"
                    }
                    m.vScrollbarWrapper.style.opacity = "0"
                }
                return
            }
            m.scrollTo(H, F, G || 0)
        },
        _wheel: function (J) {
            var H = this, I, G, F, m, K;
            if ("wheelDeltaX" in J) {
                I = J.wheelDeltaX / 12;
                G = J.wheelDeltaY / 12
            } else {
                if ("wheelDelta" in J) {
                    I = G = J.wheelDelta / 12
                } else {
                    if ("detail" in J) {
                        I = G = -J.detail * 3
                    } else {
                        return
                    }
                }
            }
            if (H.options.wheelAction == "zoom") {
                K = H.scale * Math.pow(2, 1 / 3 * (G ? G / Math.abs(G) : 0));
                if (K < H.options.zoomMin) {
                    K = H.options.zoomMin
                }
                if (K > H.options.zoomMax) {
                    K = H.options.zoomMax
                }
                if (K != H.scale) {
                    if (!H.wheelZoomCount && H.options.onZoomStart) {
                        H.options.onZoomStart.call(H, J)
                    }
                    H.wheelZoomCount++;
                    H.zoom(J.pageX, J.pageY, K, 400);
                    setTimeout(function () {
                        H.wheelZoomCount--;
                        if (!H.wheelZoomCount && H.options.onZoomEnd) {
                            H.options.onZoomEnd.call(H, J)
                        }
                    }, 400)
                }
                return
            }
            F = H.x + I;
            m = H.y + G;
            if (F > 0) {
                F = 0
            } else {
                if (F < H.maxScrollX) {
                    F = H.maxScrollX
                }
            }
            if (m > H.minScrollY) {
                m = H.minScrollY
            } else {
                if (m < H.maxScrollY) {
                    m = H.maxScrollY
                }
            }
            if (H.maxScrollY < 0) {
                H.scrollTo(F, m, 0)
            }
        },
        _transitionEnd: function (F) {
            var m = this;
            if (F.target != m.scroller) {
                return
            }
            m._unbind(a);
            m._startAni()
        },
        _startAni: function () {
            var K = this, F = K.x, m = K.y, I = Date.now(), J, H, G;
            if (K.animating) {
                return
            }
            if (!K.steps.length) {
                K._resetPos(400);
                return
            }
            J = K.steps.shift();
            if (J.x == F && J.y == m) {
                J.time = 0
            }
            K.animating = true;
            K.moved = true;
            if (K.options.useTransition) {
                K._transitionTime(J.time);
                K._pos(J.x, J.y);
                K.animating = false;
                if (J.time) {
                    K._bind(a)
                } else {
                    K._resetPos(0)
                }
                return
            }
            G = function () {
                var L = Date.now(), N, M;
                if (L >= I + J.time) {
                    K._pos(J.x, J.y);
                    K.animating = false;
                    if (K.options.onAnimationEnd) {
                        K.options.onAnimationEnd.call(K)
                    }
                    K._startAni();
                    return
                }
                L = (L - I) / J.time - 1;
                H = u.sqrt(1 - L * L);
                N = (J.x - F) * H + F;
                M = (J.y - m) * H + m;
                K._pos(N, M);
                if (K.animating) {
                    K.aniTime = q(G)
                }
            };
            G()
        },
        _transitionTime: function (m) {
            m += "ms";
            this.scroller.style[k] = m;
            if (this.hScrollbar) {
                this.hScrollbarIndicator.style[k] = m
            }
            if (this.vScrollbar) {
                this.vScrollbarIndicator.style[k] = m
            }
        },
        _momentum: function (L, F, J, m, N) {
            var K = 0.0006, G = u.abs(L) / F, H = (G * G) / (2 * K), M = 0, I = 0;
            if (L > 0 && H > J) {
                I = N / (6 / (H / G * K));
                J = J + I;
                G = G * J / H;
                H = J
            } else {
                if (L < 0 && H > m) {
                    I = N / (6 / (H / G * K));
                    m = m + I;
                    G = G * m / H;
                    H = m
                }
            }
            H = H * (L < 0 ? -1 : 1);
            M = G / K;
            return {dist: H, time: u.round(M)}
        },
        _offset: function (m) {
            var G = -m.offsetLeft, F = -m.offsetTop;
            while (m = m.offsetParent) {
                G -= m.offsetLeft;
                F -= m.offsetTop
            }
            if (m != this.wrapper) {
                G *= this.scale;
                F *= this.scale
            }
            return {left: G, top: F}
        },
        _snap: function (M, L) {
            var J = this, I, H, K, G, F, m;
            K = J.pagesX.length - 1;
            for (I = 0, H = J.pagesX.length; I < H; I++) {
                if (M >= J.pagesX[I]) {
                    K = I;
                    break
                }
            }
            if (K == J.currPageX && K > 0 && J.dirX < 0) {
                K--
            }
            M = J.pagesX[K];
            F = u.abs(M - J.pagesX[J.currPageX]);
            F = F ? u.abs(J.x - M) / F * 500 : 0;
            J.currPageX = K;
            K = J.pagesY.length - 1;
            for (I = 0; I < K; I++) {
                if (L >= J.pagesY[I]) {
                    K = I;
                    break
                }
            }
            if (K == J.currPageY && K > 0 && J.dirY < 0) {
                K--
            }
            L = J.pagesY[K];
            m = u.abs(L - J.pagesY[J.currPageY]);
            m = m ? u.abs(J.y - L) / m * 500 : 0;
            J.currPageY = K;
            G = u.round(u.max(F, m)) || 200;
            return {x: M, y: L, time: G}
        },
        _bind: function (G, F, m) {
            (F || this.scroller).addEventListener(G, this, !!m)
        },
        _unbind: function (G, F, m) {
            (F || this.scroller).removeEventListener(G, this, !!m)
        },
        destroy: function () {
            var m = this;
            m.scroller.style[l] = "";
            m.hScrollbar = false;
            m.vScrollbar = false;
            m._scrollbar("h");
            m._scrollbar("v");
            m._unbind(g, i);
            m._unbind(b);
            m._unbind(t, i);
            m._unbind(c, i);
            m._unbind(w, i);
            if (!m.options.hasTouch) {
                m._unbind("DOMMouseScroll");
                m._unbind("mousewheel")
            }
            if (m.options.useTransition) {
                m._unbind(a)
            }
            if (m.options.checkDOMChanges) {
                clearInterval(m.checkDOMTime)
            }
            if (m.options.onDestroy) {
                m.options.onDestroy.call(m)
            }
        },
        refresh: function () {
            var H = this, J, G, m, F, K = 0, I = 0;
            if (H.scale < H.options.zoomMin) {
                H.scale = H.options.zoomMin
            }
            H.wrapperW = H.wrapper.clientWidth || 1;
            H.wrapperH = H.wrapper.clientHeight || 1;
            H.minScrollY = -H.options.topOffset || 0;
            H.scrollerW = u.round(H.scroller.offsetWidth * H.scale);
            H.scrollerH = u.round((H.scroller.offsetHeight + H.minScrollY) * H.scale);
            H.maxScrollX = H.wrapperW - H.scrollerW;
            H.maxScrollY = H.wrapperH - H.scrollerH + H.minScrollY;
            H.dirX = 0;
            H.dirY = 0;
            if (H.options.onRefresh) {
                H.options.onRefresh.call(H)
            }
            H.hScroll = H.options.hScroll && H.maxScrollX < 0;
            H.vScroll = H.options.vScroll && (!H.options.bounceLock && !H.hScroll || H.scrollerH > H.wrapperH);
            H.hScrollbar = H.hScroll && H.options.hScrollbar;
            H.vScrollbar = H.vScroll && H.options.vScrollbar && H.scrollerH > H.wrapperH;
            J = H._offset(H.wrapper);
            H.wrapperOffsetLeft = -J.left;
            H.wrapperOffsetTop = -J.top;
            if (typeof H.options.snap == "string") {
                H.pagesX = [];
                H.pagesY = [];
                F = H.scroller.querySelectorAll(H.options.snap);
                for (G = 0, m = F.length; G < m; G++) {
                    K = H._offset(F[G]);
                    K.left += H.wrapperOffsetLeft;
                    K.top += H.wrapperOffsetTop;
                    H.pagesX[G] = K.left < H.maxScrollX ? H.maxScrollX : K.left * H.scale;
                    H.pagesY[G] = K.top < H.maxScrollY ? H.maxScrollY : K.top * H.scale
                }
            } else {
                if (H.options.snap) {
                    H.pagesX = [];
                    while (K >= H.maxScrollX) {
                        H.pagesX[I] = K;
                        K = K - H.wrapperW;
                        I++
                    }
                    if (H.maxScrollX % H.wrapperW) {
                        H.pagesX[H.pagesX.length] = H.maxScrollX - H.pagesX[H.pagesX.length - 1] + H.pagesX[H.pagesX.length - 1]
                    }
                    K = 0;
                    I = 0;
                    H.pagesY = [];
                    while (K >= H.maxScrollY) {
                        H.pagesY[I] = K;
                        K = K - H.wrapperH;
                        I++
                    }
                    if (H.maxScrollY % H.wrapperH) {
                        H.pagesY[H.pagesY.length] = H.maxScrollY - H.pagesY[H.pagesY.length - 1] + H.pagesY[H.pagesY.length - 1]
                    }
                }
            }
            H._scrollbar("h");
            H._scrollbar("v");
            if (!H.zoomed) {
                H.scroller.style[k] = "0";
                H._resetPos(400)
            }
        },
        scrollTo: function (m, L, K, J) {
            var I = this, H = m, G, F;
            I.stop();
            if (!H.length) {
                H = [{x: m, y: L, time: K, relative: J}]
            }
            for (G = 0, F = H.length; G < F; G++) {
                if (H[G].relative) {
                    H[G].x = I.x - H[G].x;
                    H[G].y = I.y - H[G].y
                }
                I.steps.push({x: H[G].x, y: H[G].y, time: H[G].time || 0})
            }
            I._startAni()
        },
        scrollToElement: function (m, G) {
            var F = this, H;
            m = m.nodeType ? m : F.scroller.querySelector(m);
            if (!m) {
                return
            }
            H = F._offset(m);
            H.left += F.wrapperOffsetLeft;
            H.top += F.wrapperOffsetTop;
            H.left = H.left > 0 ? 0 : H.left < F.maxScrollX ? F.maxScrollX : H.left;
            H.top = H.top > F.minScrollY ? F.minScrollY : H.top < F.maxScrollY ? F.maxScrollY : H.top;
            G = G === undefined ? u.max(u.abs(H.left) * 2, u.abs(H.top) * 2) : G;
            F.scrollTo(H.left, H.top, G)
        },
        scrollToPage: function (G, F, I) {
            var H = this, m, J;
            I = I === undefined ? 400 : I;
            if (H.options.onScrollStart) {
                H.options.onScrollStart.call(H)
            }
            if (H.options.snap) {
                G = G == "next" ? H.currPageX + 1 : G == "prev" ? H.currPageX - 1 : G;
                F = F == "next" ? H.currPageY + 1 : F == "prev" ? H.currPageY - 1 : F;
                G = G < 0 ? 0 : G > H.pagesX.length - 1 ? H.pagesX.length - 1 : G;
                F = F < 0 ? 0 : F > H.pagesY.length - 1 ? H.pagesY.length - 1 : F;
                H.currPageX = G;
                H.currPageY = F;
                m = H.pagesX[G];
                J = H.pagesY[F]
            } else {
                m = -H.wrapperW * G;
                J = -H.wrapperH * F;
                if (m < H.maxScrollX) {
                    m = H.maxScrollX
                }
                if (J < H.maxScrollY) {
                    J = H.maxScrollY
                }
            }
            H.scrollTo(m, J, I)
        },
        disable: function () {
            this.stop();
            this._resetPos(0);
            this.enabled = false;
            this._unbind(t, i);
            this._unbind(c, i);
            this._unbind(w, i)
        },
        enable: function () {
            this.enabled = true
        },
        stop: function () {
            if (this.options.useTransition) {
                this._unbind(a)
            } else {
                p(this.aniTime)
            }
            this.steps = [];
            this.moved = false;
            this.animating = false
        },
        zoom: function (m, J, I, H) {
            var F = this, G = I / F.scale;
            if (!F.options.useTransform) {
                return
            }
            F.zoomed = true;
            H = H === undefined ? 200 : H;
            m = m - F.wrapperOffsetLeft - F.x;
            J = J - F.wrapperOffsetTop - F.y;
            F.x = m - m * G + F.x;
            F.y = J - J * G + F.y;
            F.scale = I;
            F.refresh();
            F.x = F.x > 0 ? 0 : F.x < F.maxScrollX ? F.maxScrollX : F.x;
            F.y = F.y > F.minScrollY ? F.minScrollY : F.y < F.maxScrollY ? F.maxScrollY : F.y;
            F.scroller.style[k] = H + "ms";
            F.scroller.style[l] = "translate(" + F.x + "px," + F.y + "px) scale(" + I + ")" + C;
            F.zoomed = false
        },
        isReady: function () {
            return !this.moved && !this.zoomed && !this.animating
        }
    };
    function s(m) {
        if (z === "") {
            return m
        }
        m = m.charAt(0).toUpperCase() + m.substr(1);
        return z + m
    }

    n = null;
    if (typeof exports !== "undefined") {
        exports.iScroll = v
    } else {
        i.iScroll = v
    }
})(window, document);
/*!artTemplate - Template Engine*/
var template = function (a, b) {
    return template["object" == typeof b ? "render" : "compile"].apply(template, arguments)
};
(function (a, b) {
    "use strict";
    a.version = "2.0.1", a.openTag = "<%", a.closeTag = "%>", a.isEscape = !0, a.isCompress = !1, a.parser = null, a.render = function (a, b) {
        var c = d(a);
        return void 0 === c ? e({id: a, name: "Render Error", message: "No Template"}) : c(b)
    }, a.compile = function (b, d) {
        function l(c) {
            try {
                return new j(c) + ""
            } catch (f) {
                return h ? (f.id = b || d, f.name = "Render Error", f.source = d, e(f)) : a.compile(b, d, !0)(c)
            }
        }

        var g = arguments, h = g[2], i = "anonymous";
        "string" != typeof d && (h = g[1], d = g[0], b = i);
        try {
            var j = f(d, h)
        } catch (k) {
            return k.id = b || d, k.name = "Syntax Error", e(k)
        }
        return l.prototype = j.prototype, l.toString = function () {
            return "" + j
        }, b !== i && (c[b] = l), l
    }, a.helper = function (b, c) {
        a.prototype[b] = c
    }, a.onerror = function (a) {
        var c = "[template]:\n" + a.id + "\n\n[name]:\n" + a.name;
        a.message && (c += "\n\n[message]:\n" + a.message), a.line && (c += "\n\n[line]:\n" + a.line, c += "\n\n[source]:\n" + a.source.split(/\n/)[a.line - 1].replace(/^[\s\t]+/, "")), a.temp && (c += "\n\n[temp]:\n" + a.temp), b.console && console.error(c)
    };
    var c = {}, d = function (d) {
        var e = c[d];
        if (void 0 === e && "document" in b) {
            var f = document.getElementById(d);
            if (f) {
                var g = f.value || f.innerHTML;
                return a.compile(d, g.replace(/^\s*|\s*$/g, ""))
            }
        } else if (c.hasOwnProperty(d))return e
    }, e = function (b) {
        function c() {
            return c + ""
        }

        return a.onerror(b), c.toString = function () {
            return "{Template Error}"
        }, c
    }, f = function () {
        a.prototype = {
            $render: a.render, $escape: function (a) {
                return "string" == typeof a ? a.replace(/&(?![\w#]+;)|[<>"']/g, function (a) {
                    return {"<": "&#60;", ">": "&#62;", '"': "&#34;", "'": "&#39;", "&": "&#38;"}[a]
                }) : a
            }, $string: function (a) {
                return "string" == typeof a || "number" == typeof a ? a : "function" == typeof a ? a() : ""
            }
        };
        var b = Array.prototype.forEach || function (a, b) {
                for (var c = this.length >>> 0, d = 0; c > d; d++)d in this && a.call(b, this[d], d, this)
            }, c = function (a, c) {
            b.call(a, c)
        }, d = "break,case,catch,continue,debugger,default,delete,do,else,false,finally,for,function,if,in,instanceof,new,null,return,switch,this,throw,true,try,typeof,var,void,while,with,abstract,boolean,byte,char,class,const,double,enum,export,extends,final,float,goto,implements,import,int,interface,long,native,package,private,protected,public,short,static,super,synchronized,throws,transient,volatile,arguments,let,yield,undefined", e = /\/\*(?:.|\n)*?\*\/|\/\/[^\n]*\n|\/\/[^\n]*$|'[^']*'|"[^"]*"|[\s\t\n]*\.[\s\t\n]*[$\w\.]+/g, f = /[^\w$]+/g, g = RegExp(["\\b" + d.replace(/,/g, "\\b|\\b") + "\\b"].join("|"), "g"), h = /\b\d[^,]*/g, i = /^,+|,+$/g, j = function (a) {
            return a = a.replace(e, "").replace(f, ",").replace(g, "").replace(h, "").replace(i, ""), a = a ? a.split(/,+/) : []
        };
        return function (b, d) {
            function w(b) {
                return k += b.split(/\n/).length - 1, a.isCompress && (b = b.replace(/[\n\r\t\s]+/g, " ")), b = b.replace(/('|\\)/g, "\\$1").replace(/\r/g, "\\r").replace(/\n/g, "\\n"), b = q[1] + "'" + b + "'" + q[2], b + "\n"
            }

            function x(b) {
                var c = k;
                if (g ? b = g(b) : d && (b = b.replace(/\n/g, function () {
                        return k++, "$line=" + k + ";"
                    })), 0 === b.indexOf("=")) {
                    var e = 0 !== b.indexOf("==");
                    if (b = b.replace(/^=*|[\s;]*$/g, ""), e && a.isEscape) {
                        var f = b.replace(/\s*\([^\)]+\)/, "");
                        m.hasOwnProperty(f) || /^(include|print)$/.test(f) || (b = "$escape($string(" + b + "))")
                    } else b = "$string(" + b + ")";
                    b = q[1] + b + q[2]
                }
                return d && (b = "$line=" + c + ";" + b), y(b), b + "\n"
            }

            function y(a) {
                a = j(a), c(a, function (a) {
                    l.hasOwnProperty(a) || (z(a), l[a] = !0)
                })
            }

            function z(a) {
                var b;
                "print" === a ? b = s : "include" === a ? (n.$render = m.$render, b = t) : (b = "$data." + a, m.hasOwnProperty(a) && (n[a] = m[a], b = 0 === a.indexOf("$") ? "$helpers." + a : b + "===undefined?$helpers." + a + ":" + b)), o += a + "=" + b + ","
            }

            var e = a.openTag, f = a.closeTag, g = a.parser, h = b, i = "", k = 1, l = {
                $data: !0,
                $helpers: !0,
                $out: !0,
                $line: !0
            }, m = a.prototype, n = {}, o = "var $helpers=this," + (d ? "$line=0," : ""), p = "".trim, q = p ? ["$out='';", "$out+=", ";", "$out"] : ["$out=[];", "$out.push(", ");", "$out.join('')"], r = p ? "if(content!==undefined){$out+=content;return content}" : "$out.push(content);", s = "function(content){" + r + "}", t = "function(id,data){if(data===undefined){data=$data}var content=$helpers.$render(id,data);" + r + "}";
            c(h.split(e), function (a) {
                a = a.split(f);
                var c = a[0], d = a[1];
                1 === a.length ? i += w(c) : (i += x(c), d && (i += w(d)))
            }), h = i, d && (h = "try{" + h + "}catch(e){" + "e.line=$line;" + "throw e" + "}"), h = "'use strict';" + o + q[0] + h + "return new String(" + q[3] + ")";
            try {
                var u = Function("$data", h);
                return u.prototype = n, u
            } catch (v) {
                throw v.temp = "function anonymous($data) {" + h + "}", v
            }
        }
    }()
})(template, this), "function" == typeof define ? define(function (a, b, c) {
    c.exports = template
}) : "undefined" != typeof exports && (module.exports = template);
(function (exports) {
    exports.openTag = "{";
    exports.closeTag = "}";
    exports.parser = function (code) {
        code = code.replace(/^\s/, "");
        var args = code.split(" ");
        var key = args.shift();
        var keywords = exports.keywords;
        var fuc = keywords[key];
        if (fuc && keywords.hasOwnProperty(key)) {
            args = args.join(" ");
            code = fuc.call(code, args)
        } else {
            if (exports.prototype.hasOwnProperty(key)) {
                args = args.join(",");
                code = "==" + key + "(" + args + ");"
            } else {
                code = code.replace(/[\s;]*$/, "");
                code = "=" + code
            }
        }
        return code
    };
    exports.keywords = {
        "if": function (code) {
            return "if(" + code + "){"
        }, "else": function (code) {
            code = code.split(" ");
            if (code.shift() === "if") {
                code = " if(" + code.join(" ") + ")"
            } else {
                code = ""
            }
            return "}else" + code + "{"
        }, "/if": function () {
            return "}"
        }, "each": function (code) {
            code = code.split(" ");
            var object = code[0] || "$data";
            var as = code[1] || "as";
            var value = code[2] || "$value";
            var index = code[3] || "$index";
            var args = value + "," + index;
            if (as !== "as") {
                object = "[]"
            }
            return "$each(" + object + ",function(" + args + "){"
        }, "/each": function () {
            return "});"
        }, "echo": function (code) {
            return "print(" + code + ");"
        }, "include": function (code) {
            code = code.split(" ");
            var id = code[0];
            var data = code[1];
            var args = id + (data ? ("," + data) : "");
            return "include(" + args + ");"
        }
    };
    exports.helper("$each", function (data, callback) {
        var isArray = Array.isArray || function (obj) {
                return Object.prototype.toString.call(obj) === "[object Array]"
            };
        if (isArray(data)) {
            for (var i = 0, len = data.length; i < len; i++) {
                callback.call(data, data[i], i, data)
            }
        } else {
            for (i in data) {
                callback.call(data, data[i], i)
            }
        }
    })
})(template);
(function (j) {
    try {
        document.createEvent("TouchEvent")
    } catch (l) {
        var o = {}, d = {"touchstart": "mousedown", "touchend": "mouseup", "touchmove": "mousemove"};

        function k(r, v, q) {
            if ((typeof r) == "object") {
                return [r]
            }
            var s = r.match(/([^.]*)(\..*|$)/), u = s[0], r = s[1], e = s[2], t = d[r];
            result = [(t || r) + e];
            if (arguments.length > 1) {
                if (t) {
                    v = f(r, v, q)
                }
                result.push(v)
            }
            return result
        }

        function f(q, r, e) {
            return o[r] = function (s) {
                if (s.liveFired) {
                    e = this
                }
                if (s.button) {
                    return false
                }
                s.touches = [{
                    length: 1,
                    clientX: s.clientX,
                    clientY: s.clienty,
                    pageX: s.pageX,
                    pageY: s.pageY,
                    screenX: s.screenX,
                    screenY: s.screenY,
                    target: s.target
                }];
                s.touchtype = q;
                return r.apply(e, [s])
            }
        }

        var g = j.fn.bind;
        j.fn.bind = function (e, q) {
            return g.apply(this, k(e, q, this))
        };
        var i = j.fn.unbind;
        j.fn.unbind = function (q, r) {
            if (!q) {
                i.apply(this);
                return
            }
            var e = i.apply(this, k(q).concat([o[r] || r]));
            delete (o[r]);
            return e
        };
        var m = j.fn.one;
        j.fn.one = function (e, q) {
            return m.apply(this, k(e, q, this))
        };
        var n = j.fn.delegate;
        j.fn.delegate = function (e, q, r) {
            return n.apply(this, [e].concat(k(q, r, this)))
        };
        var h = j.fn.undelegate;
        j.fn.undelegate = function (q, r, s) {
            var e = h.apply(this, [q].concat(k(r), [o[s] || s]));
            delete (o[s]);
            return e
        };
        var p = j.fn.live;
        j.fn.live = function (e, q) {
            return p.apply(this, k(e, q, this))
        };
        var a = j.fn.die;
        j.fn.die = function (q, r) {
            var e = a.apply(this, k(q).concat([o[r] || r]));
            delete (o[r]);
            return e
        };
        var b = j.fn.trigger;
        j.fn.trigger = function (e, q) {
            return b.apply(this, k(e).concat([q]))
        };
        var c = j.fn.triggerHandler;
        j.fn.triggerHandler = function (e, q) {
            return c.apply(this, k(e).concat([q]))
        }
    }
})(Zepto);
var Jingle = J = {
    version: "0.4",
    $: window.Zepto,
    settings: {
        appType: "muti",
        transitionType: "slide",
        transitionTime: 250,
        transitionTimingFunc: "ease-in",
        showWelcome: false,
        showPageLoading: true,
        basePagePath: "html/",
        remotePage: {}
    },
    mode: window.innerWidth < 800 ? "phone" : "tablet",
    hasTouch: "ontouchstart" in window,
    launchCompleted: false,
    hasMenuOpen: false,
    hasPopupOpen: false,
    isWebApp: location.protocol == "http:",
    launch: function (b) {
        $.extend(this.settings, b);
        var a = window.localStorage.getItem("hasShowWelcome");
        if (!a && this.settings.showWelcome) {
            this.Welcome.show()
        }
        if (this.settings.appType == "single") {
            this.Router.init()
        } else {
            $(document).on("click", 'a[data-target="back"]', function (c) {
                c.preventDefault();
                window.history.go(-1)
            })
        }
        this.Element.init();
        this.Element.initControlGroup();
        this.Menu.init();
        this.Selected.init()
    }
};
J.Element = (function (c) {
    var d = {
        "icon": "[data-icon]",
        "scroll": '[data-scroll="true"]',
        "toggle": ".toggle",
        "range": "[data-rangeinput]",
        "progress": "[data-progress]",
        "count": "[data-count]",
        "checkbox": "[data-checkbox]"
    };
    var l = function (m) {
        if (!m) {
            c(document).on("articleshow", "article", function () {
                J.Element.initScroll(this)
            })
        }
        var n = c(m || "body");
        if (n.length == 0) {
            return
        }
        c.map(b(n, d.icon), i);
        c.map(b(n, d.toggle), f);
        c.map(b(n, d.range), e);
        c.map(b(n, d.progress), h);
        c.map(b(n, d.count), g);
        c.map(b(n, d.checkbox), a)
    };
    var j = function () {
        c(document).on("tap", "ul.control-group li", function () {
            var m = c(this);
            if (m.hasClass("active")) {
                return
            }
            m.addClass("active").siblings(".active").removeClass("active").parent().trigger("change", [m])
        })
    };
    var b = function (n, m) {
        return n.find(m).add(n.filter(m))
    };
    var k = function (m) {
        c.map(b(c(m), d.scroll), function (n) {
            J.Scroll(n)
        })
    };
    var i = function (p) {
        var n = c(p), m = n.children("i.icon"), o = n.data("icon");
        if (m.length > 0) {
            m.attr("class", "icon " + o)
        } else {
            n.prepend('<i class="icon ' + o + '"></i>')
        }
    };
    var f = function (o) {
        var n = c(o), p;
        if (n.find("div.toggle-handle").length > 0) {
            return
        }
        var m = n.attr("name");
        if (m) {
            n.append('<input style="display: none;" name="' + m + '" value="' + n.hasClass("active") + '"/>')
        }
        n.append('<div class="toggle-handle"></div>');
        p = n.find("input");
        n.tap(function () {
            var q;
            if (n.hasClass("active")) {
                n.removeClass("active");
                q = false
            } else {
                n.addClass("active");
                q = true
            }
            p.val(q);
            n.trigger("toggle")
        })
    };
    var e = function (q) {
        var p = c(q), t;
        var s = c('input[type="range"]', q);
        var r = p.data("rangeinput");
        var n = c('<input type="text" name="test" value="' + s.val() + '"/>');
        if (r == "left") {
            t = n.prependTo(p)
        } else {
            t = n.appendTo(p)
        }
        var m = parseInt(s.attr("max"), 10);
        var o = parseInt(s.attr("min"), 10);
        s.change(function () {
            t.val(s.val())
        });
        t.on("input", function () {
            var u = parseInt(t.val(), 10);
            u = u > m ? m : (u < o ? o : u);
            s.val(u);
            t.val(u)
        })
    };
    var h = function (o) {
        var n = c(o), p;
        var m = parseFloat(n.data("progress")) + "%";
        var q = n.data("title") || "";
        p = n.find("div.bar");
        if (p.length == 0) {
            p = c('<div class="bar"></div>').appendTo(n)
        }
        p.width(m).text(q + m);
        if (m == "100%") {
            p.css("border-radius", "10px")
        }
    };
    var g = function (r) {
        var o = c(r), n;
        var s = parseInt(o.data("count"));
        var m = o.data("orient");
        var q = (m == "left") ? "left" : "";
        var p = c('<span class="count ' + q + '">' + s + "</span>");
        n = o.find("span.count");
        if (n.length > 0) {
            n.text(s)
        } else {
            n = p.appendTo(o)
        }
        if (s == 0) {
            n.hide()
        }
    };
    var a = function (n) {
        var m = c(n);
        var o = m.data("checkbox");
        if (m.find("i.icon").length > 0) {
            return
        }
        m.prepend('<i class="icon checkbox-' + o + '"></i>');
        m.on("tap", function () {
            var p = (m.data("checkbox") == "checked") ? "unchecked" : "checked";
            m.find("i.icon").attr("class", "icon checkbox-" + p);
            m.data("checkbox", p);
            m.trigger("change")
        })
    };
    return {
        init: l,
        initControlGroup: j,
        initIcon: i,
        initToggle: f,
        initProgress: h,
        initRange: e,
        initBadge: g,
        initScroll: k
    }
})(J.$);
J.Menu = (function (e) {
    var d, g, c;
    var f = function () {
        d = e("#aside_container");
        g = e("#section_container");
        c = e('<div id="section_container_mask"></div>').appendTo("#section_container");
        c.on("tap", a);
        d.on("swipeRight", "aside", function () {
            if (e(this).data("position") == "right") {
                a()
            }
        });
        d.on("swipeLeft", "aside", function () {
            if (e(this).data("position") != "right") {
                a()
            }
        });
        d.on("tap", ".aside-close", a)
    };
    var b = function (j) {
        var i = e(j).addClass("active"), n = i.data("transition"), h = i.data("position") || "left", l = i.data("show-close"), k = i.width(), m = h == "left" ? k + "px" : "-" + k + "px";
        if (l && i.find("div.aside-close").length == 0) {
            i.append('<div class="aside-close icon close"></div>')
        }
        J.Element.initScroll(i);
        if (n == "overlay") {
            J.anim(i, {translateX: "0%"})
        } else {
            if (n == "reveal") {
                J.anim(g, {translateX: m})
            } else {
                J.anim(i, {translateX: "0%"});
                J.anim(g, {translateX: m})
            }
        }
        e("#section_container_mask").show();
        J.hasMenuOpen = true
    };
    var a = function (k, n) {
        var i = e("#aside_container aside.active"), m = i.data("transition"), h = i.data("position") || "left", l = h == "left" ? "-100%" : "100%";
        var j = function () {
            i.removeClass("active");
            J.hasMenuOpen = false;
            n && n.call(this)
        };
        if (m == "overlay") {
            J.anim(i, {translateX: l}, k, j)
        } else {
            if (m == "reveal") {
                J.anim(g, {translateX: "0"}, k, j)
            } else {
                J.anim(i, {translateX: l}, k);
                J.anim(g, {translateX: "0"}, k, j)
            }
        }
        e("#section_container_mask").hide()
    };
    return {init: f, show: b, hide: a}
})(J.$);
J.Page = (function (d) {
    var c = function (f) {
        return f.indexOf("#") == 0 ? f.substr(1) : f
    };
    var a = function (j, m) {
        var k = {}, i, g = true;
        if (d.type(j) == "object") {
            j = j.tag;
            k = j.param;
            i = j.query
        }
        var h = d(j).data("query");
        if (d(j).length == 1 && h == i) {
            if (h == i) {
                m();
                return
            } else {
                g = false
            }
        }
        var l = c(j);
        var f = J.settings.remotePage[j];
        f || (f = J.settings.basePagePath + l + ".html");
        J.settings.showPageLoading && J.showMask();
        e(f, k, function (n) {
            J.settings.showPageLoading && J.hideMask();
            if (!g) {
                d(j).remove()
            }
            d("#section_container").append(n);
            d(j).trigger("pageload").data("query", i);
            J.Element.init(j);
            m()
        })
    };
    var b = function (f, g) {
        var h = J.Util.parseHash(window.location.hash).param;
        e(f, h, function (i) {
            d(g).html(i);
            J.Element.init(g)
        })
    };
    var e = function (f, g, h) {
        return d.ajax({
            url: f, timeout: 20000, data: g, success: function (i) {
                h && h(i)
            }
        })
    };
    return {load: a, loadSection: b, loadContent: e}
})(J.$);
J.Router = (function (d) {
    var f = [];
    var l = function () {
        d(window).on("popstate", a);
        d(document).on("click", "a", function (n) {
            var m = d(this).data("target");
            if (!m || m != "link") {
                n.preventDefault();
                return false
            }
        });
        d(document).on("tap", "a", function (n) {
            var m = d(this).data("target");
            if (!m) {
                n.preventDefault()
            } else {
                if (m != "link") {
                    n.preventDefault();
                    i.call(this)
                }
            }
        });
        h()
    };
    var h = function () {
        var m = location.hash;
        var n = d("#section_container section.active");
        c("#" + n.attr("id"));
        n.trigger("pageinit").trigger("pageshow").data("init", true).find("article.active").trigger("articleshow");
        if (m != "") {
            k(m)
        }
    };
    var a = function (n) {
        if (n.state && n.state.hash) {
            var m = n.state.hash;
            if (f[1] && m === f[1].hash) {
                J.Menu.hide();
                J.Popup.close();
                e()
            } else {
                return
            }
        } else {
            return
        }
    };
    var i = function () {
        var o = d(this), n = o.attr("data-target"), m = o.attr("href");
        switch (n) {
            case"section":
                k(m);
                break;
            case"article":
                g(m, o);
                break;
            case"menu":
                b(m);
                break;
            case"back":
                e();
                break
        }
    };
    var k = function (m) {
        if (J.hasMenuOpen) {
            J.Menu.hide(200, function () {
                k(m)
            });
            return
        }
        var n = J.Util.parseHash(m);
        if (f[0].tag === n.tag) {
            return
        }
        J.Page.load(n, function () {
            j(f[0].tag, n.tag);
            c(m)
        })
    };
    var e = function () {
        j(f.shift().tag, f[0].tag, true);
        window.history.replaceState(f[0], "", f[0].hash)
    };
    var j = function (o, n, m) {
        J.Transition.run(o, n, m)
    };
    var c = function (m) {
        var n = J.Util.parseHash(m);
        f.unshift(n);
        window.history.pushState(n, "", m)
    };
    var g = function (n, o) {
        var p = d(n);
        if (p.hasClass("active")) {
            return
        }
        o.addClass("active").siblings(".active").removeClass("active");
        var m = p.addClass("active").siblings(".active").removeClass("active");
        p.trigger("articleshow");
        m.trigger("articlehide")
    };
    var b = function (m) {
        J.hasMenuOpen ? J.Menu.hide() : J.Menu.show(m)
    };
    return {init: l, goTo: k, showArticle: g, back: e}
})(J.$);
J.Service = (function (f) {
    var g = "JINGLE_POST_DATA", d = "JINGLE_GET_";
    var l = function (s) {
        if (s.type == "post") {
            b(s)
        } else {
            r(s)
        }
    };
    var b = function (s) {
        if (J.offline) {
            a(s.url, s.data);
            s.success("数据已存至本地")
        } else {
            f.ajax(s)
        }
    };
    var r = function (t) {
        var u = t.url + JSON.stringify(t.data);
        if (J.offline) {
            var s = e(u);
            if (s) {
                t.success(s.data, u, s.cacheTime)
            } else {
                t.success(s)
            }
        } else {
            var v = t.success;
            t.success = function (w) {
                j(u, w);
                v(w, u)
            };
            f.ajax(t)
        }
    };
    var e = function (s) {
        return JSON.parse(window.localStorage.getItem(d + s))
    };
    var j = function (t, s) {
        var u = {data: s, cacheTime: new Date()};
        window.localStorage.setItem(d + t, JSON.stringify(u))
    };
    var a = function (t, s) {
        var u = i();
        u = u || {};
        u[t] = {data: s, createdTime: new Date()};
        window.localStorage.setItem(g, JSON.stringify(u))
    };
    var i = function (s) {
        var t = JSON.parse(window.localStorage.getItem(g));
        return (t && s) ? t[s] : t
    };
    var h = function (s) {
        if (s) {
            var t = i();
            delete t[s];
            window.localStorage.setItem(g, JSON.stringify(t))
        } else {
            window.localStorage.removeItem(g)
        }
    };
    var c = function (u, v, t) {
        var s = i(u).data;
        f.ajax({
            url: u, contentType: "application/json", data: s, type: "post", success: function () {
                v(u)
            }, error: function () {
                t(u)
            }
        })
    };
    var n = function (v, u) {
        var s = i();
        for (var t in s) {
            c(t, v, u)
        }
        h()
    };

    function q(u, v, w, t) {
        var s = !f.isFunction(v);
        return {
            url: u,
            data: s ? v : undefined,
            success: !s ? v : f.isFunction(w) ? w : undefined,
            dataType: s ? t || w : w
        }
    }

    var p = function (t, u, v, s) {
        return l(q.apply(null, arguments))
    };
    var o = function (u, v, w, s) {
        var t = q.apply(null, arguments);
        t.type = "POST";
        return l(t)
    };
    var k = function (t, u, v) {
        var s = q.apply(null, arguments);
        s.dataType = "json";
        return l(s)
    };
    var m = function () {
        var v = window.localStorage;
        var u = [];
        for (var t = 0; t < v.length; t++) {
            var s = v.key(t);
            s.indexOf(d) == 0 && u.push(s)
        }
        for (var t = 0; t < u.length; t++) {
            v.removeItem(u[t])
        }
        v.removeItem(g)
    };
    return {
        ajax: l,
        get: p,
        post: o,
        getJSON: k,
        getUnPostData: i,
        removeUnPostData: h,
        syncPostData: c,
        syncAllPostData: n,
        getCacheData: e,
        saveCacheData: j,
        clear: m
    }
})(J.$);
J.Template = (function (c) {
    var a = function (h, i, g) {
        var f = '<div class="back-mask"><div class="icon ' + g + '"></div><div>' + i + "</div></div>";
        c(h).html(f)
    };
    var d = function (f) {
        a(f, "没有找到相关数据", "drawer")
    };
    var e = function (f) {
        a(f, "加载中...", "cloud-download")
    };
    var b = function (f, h, k, j) {
        var i = c(f), j = j || "replace";
        if (c.type(k) == "array" && k.length == 0) {
            d(i)
        } else {
            var g = c(template(h, k));
            if (j == "replace") {
                i.html(g)
            } else {
                i.append(g)
            }
            J.Element.init(g)
        }
    };
    return {render: b, background: a, loading: e, no_result: d}
})(J.$);
J.Toast = (function (d) {
    var c = 5000;
    var j = {
        toast: '<a href="#">{value}</a>',
        success: '<a href="#"><i class="icon checkmark-circle"></i>{value}</a>',
        error: '<a href="#">{value}</a></div>',
        info: '<a href="#"><i class="icon info-2"></i>{value}</a>'
    };
    var g = "toast", b, a;
    var h = function () {
        d("body").append('<div id="jingle_toast"></div>');
        b = d("#jingle_toast");
        e()
    };
    var f = function () {
        J.anim(b, "scaleOut", function () {
            b.hide();
            b.empty()
        })
    };
    var i = function (k, m, l) {
        if (a) {
            clearTimeout(a)
        }
        g = k;
        b.attr("class", k).html(j[k].replace("{value}", m)).show();
        J.anim(b, "scaleIn");
        if (l !== 0) {
            a = setTimeout(f, l || c)
        }
    };
    var e = function () {
        b.on("tap", '[data-target="close"]', function () {
            f()
        })
    };
    h();
    return {show: i, hide: f}
})(J.$);
J.Transition = (function (g) {
    var j, b, c, a, f = {
        slide: [["slideLeftOut", "slideLeftIn"], ["slideRightOut", "slideRightIn"]],
        cover: [["", "slideLeftIn"], ["slideRightOut", ""]],
        slideUp: [["", "slideUpIn"], ["slideDownOut", ""]],
        slideDown: [["", "slideDownIn"], ["slideUpOut", ""]],
        popup: [["", "scaleIn"], ["scaleOut", ""]]
    };
    var i = function () {
        b.trigger("beforepagehide", [j]);
        c.trigger("beforepageshow", [j]);
        var l = a[0] || "empty", k = a[1] || "empty";
        b.bind("webkitAnimationEnd.jingle", h);
        b.addClass("anim " + l);
        c.addClass("anim animating " + k)
    };
    var h = function () {
        b.off("webkitAnimationEnd.jingle");
        c.off("webkitAnimationEnd.jingle");
        b.attr("class", "");
        c.attr("class", "active");
        if (!c.data("init")) {
            c.trigger("pageinit");
            c.data("init", true)
        }
        b.trigger("pagehide", [j]);
        c.trigger("pageshow", [j]);
        b.find("article.active").trigger("articlehide");
        c.find("article.active").trigger("articleshow")
    };
    var e = function (n, m, k) {
        g(":focus").trigger("blur");
        j = k;
        b = g(n);
        c = g(m);
        var l = j ? b.attr("data-transition") : c.attr("data-transition");
        l = l || J.settings.transitionType;
        a = j ? f[l][1] : f[l][0];
        i()
    };
    var d = function (l, n, m, o, k) {
        if (f[l]) {
            console.error("该转场动画已经存在，请检查你自定义的动画名称(名称不能重复)");
            return
        }
        f[l] = [[n, m], [o, k]]
    };
    return {run: e, add: d}
})(J.$);
J.Util = (function (c) {
    var a = function (k) {
        var e, j, l = {};
        var d = k.split("?");
        e = d[0];
        if (d.length > 1) {
            var f, h;
            j = d[1];
            f = j.split("&");
            for (var g = 0; g < f.length; g++) {
                if (!f[g]) {
                    continue
                }
                h = f[g].split("=");
                l[h[0]] = h[1]
            }
        }
        return {hash: k, tag: e, query: j, param: l}
    };
    var b = function (e, f) {
        var g = {
            "M+": e.getMonth() + 1,
            "d+": e.getDate(),
            "h+": e.getHours(),
            "m+": e.getMinutes(),
            "s+": e.getSeconds(),
            "q+": Math.floor((e.getMonth() + 3) / 3),
            "S": e.getMilliseconds()
        };
        if (/(y+)/.test(f)) {
            f = f.replace(RegExp.$1, (e.getFullYear() + "").substr(4 - RegExp.$1.length))
        }
        for (var d in g) {
            if (new RegExp("(" + d + ")").test(f)) {
                f = f.replace(RegExp.$1, RegExp.$1.length == 1 ? g[d] : ("00" + g[d]).substr(("" + g[d]).length))
            }
        }
        return f
    };
    return {parseHash: a, formatDate: b}
})(J.$);
J.Welcome = (function (b) {
    var c = function () {
        b.ajax({
            url: J.settings.basePagePath + "welcome.html", timeout: 5000, async: false, success: function (d) {
                b("body").append(d);
                new J.Slider("#jingle_welcome")
            }
        })
    };
    var a = function () {
        J.anim("#jingle_welcome", "slideLeftOut", function () {
            b(this).remove();
            window.localStorage.setItem("hasShowWelcome", true)
        })
    };
    return {show: c, hide: a}
})(J.$);
J.anim = function (b, h, g, f, p) {
    var m, l, n;
    var k = arguments.length;
    for (var j = 2; j < k; j++) {
        var o = arguments[j];
        var q = $.type(o);
        q == "number" ? (m = o) : (q == "string" ? (l = o) : (q == "function") ? (n = o) : null)
    }
    $(b).animate(h, m || J.settings.transitionTime, l || J.settings.transitionTimingFunc, n)
};
J.showMask = function (a) {
    J.Popup.loading(a)
};
J.hideMask = function () {
    J.Popup.close(true)
};
J.showToast = function (c, a, b) {
    a = a || "toast";
    J.Toast.show(a, c, b)
};
J.hideToast = function () {
    J.Toast.hide()
};
J.alert = function (b, a) {
    J.Popup.alert(b, a)
};
J.customAlert = function (a) {
    J.Popup.customAlert(a)
};
J.confirm = function (d, b, c, a) {
    J.Popup.confirm(d, b, c, a)
};
J.customConfirm = function (f, d, b, c, e, a) {
    J.Popup.customConfirm(f, d, b, c, e, a)
};
J.popup = function (a) {
    J.Popup.show(a)
};
J.closePopup = function () {
    J.Popup.close()
};
J.popover = function (a, d, b, c) {
    J.Popup.popover(a, d, b, c)
};
J.tmpl = function (a, b, d, c) {
    J.Template.render(a, b, d, c)
};
J.Popup = (function (e) {
    var o, i, q, r, n = {
        "top": {top: 0, left: 0, right: 0},
        "top-second": {top: "44px", left: 0, right: 0},
        "center": {top: "50%", left: "5%", right: "5%", "border-radius": "3px"},
        "bottom": {bottom: 0, left: 0, right: 0},
        "bottom-second": {bottom: "51px", left: 0, right: 0}
    }, a = {
        top: ["slideDownIn", "slideUpOut"],
        bottom: ["slideUpIn", "slideDownOut"],
        defaultAnim: ["bounceIn", "bounceOut"]
    }, m = {
        alert: '<a data-target="closePopup"><img class="w100" src="{title}" /></a>',
        customAlert: '<div class="custom-content">{content}</div>',
        confirm: '<div class="popup-title">{title}</div><div class="popup-content">{content}</div><div id="popup_btn_container"><a class="cancel" data-icon="close">{cancel}</a><a data-icon="checkmark">{ok}</a></div>',
        customConfirm: '<div class="popup-title">{title}</div><div class="popup-content">{content}</div><div id="popup_btn_container">{cancel}{ok}</div>',
        loading: '<i class="icon spinner"></i><p>{title}</p>'
    };
    var j = function () {
        e("body").append('<div id="jingle_popup"></div><div id="jingle_popup_mask"></div>');
        i = e("#jingle_popup_mask");
        o = e("#jingle_popup");
        d()
    };
    var s = function (v) {
        var x = {
            height: undefined,
            width: undefined,
            opacity: 0.3,
            url: null,
            tplId: null,
            tplData: null,
            html: "",
            pos: "center",
            clickMask2Close: false,
            showCloseBtn: true,
            arrowDirection: undefined,
            animation: false,
            timingFunc: "linear",
            duration: 200,
            onShow: undefined
        };
        e.extend(x, v);
        r = x.clickMask2Close;
        i.css("opacity", x.opacity);
        o.attr({"style": "", "class": ""});
        x.width && o.width(x.width);
        x.height && o.height(x.height);
        var y = e.type(x.pos);
        if (y == "object") {
            o.css(x.pos);
            q = a["defaultAnim"]
        } else {
            if (y == "string") {
                if (n[x.pos]) {
                    o.css(n[x.pos]);
                    var u = x.pos.indexOf("top") > -1 ? "top" : (x.pos.indexOf("bottom") > -1 ? "bottom" : "defaultAnim");
                    q = a[u]
                } else {
                    o.addClass(x.pos);
                    q = a["defaultAnim"]
                }
            } else {
                console.error("错误的参数！");
                return
            }
        }
        i.show();
        var w;
        if (x.html) {
            w = x.html
        } else {
            if (x.url) {
                w = J.Page.loadContent(x.url)
            } else {
                if (x.tplId) {
                    w = template(x.tplId, x.tplData)
                }
            }
        }
        if (x.showCloseBtn) {
            w += '<div id="tag_close_popup" data-target="closePopup" class="icon cancel-circle"></div>'
        }
        if (x.arrowDirection) {
            o.addClass("arrow " + x.arrowDirection);
            o.css("padding", "8px");
            if (x.arrowDirection == "top" || x.arrowDirection == "bottom") {
                q = a[x.arrowDirection]
            }
        }
        o.html(w).show();
        J.Element.init(o);
        x.onShow && x.onShow.call(o);
        if (x.pos == "center") {
            var t = o.height();
            o.css("margin-top", "-" + t / 2 + "px")
        }
        J.Element.init(o);
        if (x.animation) {
            J.anim(o, q[0], x.duration, x.timingFunc)
        }
        J.hasPopupOpen = true
    };
    var k = function (t) {
        i.hide();
        if (q && !t) {
            o.hide().empty();
            J.hasPopupOpen = false
        } else {
            o.hide().empty();
            J.hasPopupOpen = false
        }
    };
    var d = function () {
        i.on("tap", function () {
            if (r) {
                k()
            }
        });
        o.on("tap", '[data-target="closePopup"]', function () {
            k()
        })
    };
    var b = function (v, u) {
        var t = m.alert.replace("{title}", v).replace("{content}", u).replace("{ok}", "确定");
        s({html: t, pos: "center", clickMask2Close: false, showCloseBtn: false})
    };
    var p = function (u) {
        var t = m.customAlert.replace("{content}", u);
        s({html: t, pos: "center", clickMask2Close: false, showCloseBtn: false})
    };
    var h = function (x, v, w, t) {
        var u = m.confirm.replace("{title}", x).replace("{content}", v).replace("{cancel}", "取消").replace("{ok}", "确定");
        s({html: u, pos: "center", clickMask2Close: false, showCloseBtn: false});
        e('#popup_btn_container [data-icon="checkmark"]').tap(function () {
            k();
            w.call(this)
        });
        e('#popup_btn_container [data-icon="close"]').tap(function () {
            k();
            t.call(this)
        })
    };
    var g = function (z, x, v, w, y, t) {
        var u = m.customConfirm.replace("{title}", z).replace("{content}", x).replace("{cancel}", v).replace("{ok}", w);
        s({html: u, pos: "center", clickMask2Close: false, showCloseBtn: false});
        e('#popup_btn_container [data="ok"]').tap(function () {
            k();
            y.call(this)
        });
        e('#popup_btn_container [data="cancel"]').tap(function () {
            k();
            t.call(this)
        })
    };
    var f = function (t, w, u, v) {
        s({html: t, pos: w, showCloseBtn: false, arrowDirection: u, onShow: v})
    };
    var c = function (u) {
        var t = m.loading.replace("{title}", u || "加载中...");
        s({html: t, pos: "loading", opacity: 0, animation: false, clickMask2Close: false})
    };
    var l = function (u) {
        var t = '<div class="actionsheet">';
        e.each(u, function (v, w) {
            t += '<ul class="list">' + '<li class="text-center">' + '<a href="' + w.href + '" data-target="' + w.target + '" class="font-type" style="color:' + w.color + ';">' + w.text + "</a>" + "</li>"
        });
        t += "</ul></div>";
        s({
            html: t, pos: "bottom", showCloseBtn: false, onShow: function () {
                e(this).find("button").each(function (w, v) {
                    e(v).on("tap", function () {
                        if (u[w] && u[w].handler) {
                            u[w].handler.call(v)
                        }
                        k()
                    })
                })
            }
        })
    };
    j();
    return {
        show: s,
        close: k,
        alert: b,
        customAlert: p,
        confirm: h,
        customConfirm: g,
        popover: f,
        loading: c,
        actionsheet: l
    }
})(J.$);
J.Selected = (function (b) {
    var a = "[data-selected]", d, e;
    var c = function () {
        b(document).on("touchstart.selected", a, function () {
            var f = b(this);
            e = setTimeout(function () {
                d = f.addClass(f.data("selected"))
            }, 50)
        });
        b(document).on("touchmove.selected touchend.selected touchcancel.selected", function () {
            e && clearTimeout(e);
            if (d) {
                d.removeClass(d.data("selected"));
                d = null
            }
        })
    };
    return {init: c}
})(J.$);
J.Cache = (function (c) {
    var j = "_J_P_", i = "_J_";
    var e = function (k, m) {
        var l = {data: m, cacheTime: new Date()};
        window.localStorage.setItem(i + k, JSON.stringify(l))
    };
    var a = function (k) {
        return JSON.parse(window.localStorage.getItem(i + k))
    };
    var f = function (l, k) {
        var m = h();
        m = m || {};
        m[l] = {data: k, createdTime: new Date()};
        window.localStorage.setItem(j, JSON.stringify(m))
    };
    var h = function (k) {
        var l = JSON.parse(window.localStorage.getItem(j));
        return (l && k) ? l[k] : l
    };
    var g = function (k) {
        if (k) {
            var l = h();
            delete l[k];
            window.localStorage.setItem(j, JSON.stringify(l))
        } else {
            window.localStorage.removeItem(j)
        }
    };
    var b = function (n, q, m) {
        var o, l = 0;
        if (c.type(n) == "string") {
            o = 1;
            p(n)
        } else {
            var k = h();
            if (!k) {
                return
            }
            o = k.length;
            for (var n in k) {
                p(n)
            }
        }
        function p(r) {
            var s = h(r).data;
            c.ajax({
                url: r, contentType: "application/json", data: s, type: "post", success: function () {
                    l++;
                    g(r);
                    if (l == o) {
                        q(r)
                    }
                }, error: function () {
                    m(r)
                }
            })
        }
    };
    var d = function () {
        var l = window.localStorage;
        for (var k in l) {
            if (k.indexOf(i) == 0) {
                l.removeItem(k)
            }
        }
        l.removeItem(j)
    };
    return {get: a, save: e, getPost: h, savePost: f, removePost: g, syncPost: b, clear: d}
})(J.$);
(function (a) {
    var b = function (r, d) {
        var n = {
            months: ["01月", "02月", "03月", "04月", "05月", "06月", "07月", "08月", "09月", "10月", "11月", "12月"],
            days: ["日", "一", "二", "三", "四", "五", "六"],
            swipeable: true,
            date: new Date(),
            onRenderDay: undefined,
            onSelect: undefined
        }, q = this, v = a(r), e, c, i, l, u, p;
        var k = function () {
            q.settings = a.extend({}, n, d);
            u = q.settings.date.getFullYear();
            p = q.settings.date.getMonth();
            l = new Date(u, p, q.settings.date.getDate());
            j();
            h()
        };
        var s = function (w) {
            return (new Date(w.getFullYear(), w.getMonth(), 1)).getDay()
        };
        var t = function (w) {
            return (new Date(w.getFullYear(), w.getMonth() + 1, 0)).getDate()
        };
        var j = function () {
            var x = "";
            x += '<div class="jingle-calendar">';
            x += f(u, p);
            x += g();
            x += '<div class="jingle-calendar-body">';
            x += m(l);
            x += "</div></div>";
            v.html(x);
            var w = v.find("span");
            e = w.eq(0);
            c = w.eq(1);
            i = v.find(".jingle-calendar-body")
        };
        var f = function (x, y) {
            var w = '<div class="jingle-calendar-nav">';
            w += '<div> <i class="icon previous" data-year=' + x + "></i><span>" + x + '</span><i class="icon next" data-year=' + x + "></i></div>";
            w += '<div ><i class="icon previous" data-month=' + y + "></i> <span>" + q.settings.months[y] + '</span><i class="icon next" data-month=' + y + "></i></div>";
            w += "</div>";
            return w
        };
        var g = function () {
            var x = "<table><thead><tr>";
            for (var w = 0; w < 7; w++) {
                x += "<th>" + q.settings.days[w] + "</th>"
            }
            x += "</tr></thead></table>";
            return x
        };
        var m = function (y) {
            var w = s(y), D = t(y), C = Math.ceil((w + D) / 7), B, A = "";
            u = y.getFullYear();
            p = y.getMonth();
            B = new Date(u, p, 1 - w);
            A += "<table><tbody>";
            for (var z = 0; z < C; z++) {
                A += "<tr>";
                for (var x = 0; x < 7; x++) {
                    A += o(B, p);
                    B.setDate(B.getDate() + 1)
                }
                A += "</tr>"
            }
            A += "</tbody></table>";
            return A
        };
        var o = function (x, B) {
            var z = (x.getMonth() !== B);
            var w = q.format(x);
            var A = (q.format(new Date()) == w) ? "active disabled" : "";
            if (x < q.settings.date) {
                A += " disabled"
            }
            var y = x.getDate();
            if (q.settings.onRenderDay) {
                y = q.settings.onRenderDay.call(null, y, w)
            }
            return z ? "<td>&nbsp;</td>" : '<td data-selected="selected" class="' + A + '" data-date= ' + w + ">" + y + "</td>"
        };
        var h = function () {
            var w, x;
            v.on("tap", function (A) {
                w = a(A.target);
                if (w.is("[data-year].next")) {
                    l.setFullYear(l.getFullYear() + 1);
                    q.refresh(l)
                } else {
                    if (w.is("[data-year].previous")) {
                        l.setFullYear(l.getFullYear() - 1);
                        q.refresh(l)
                    } else {
                        if (w.is("[data-month].next")) {
                            l.setMonth(l.getMonth() + 1);
                            q.refresh(l)
                        } else {
                            if (w.is("[data-month].previous")) {
                                l.setMonth(l.getMonth() - 1);
                                q.refresh(l)
                            }
                        }
                    }
                }
                x = w.closest("td");
                if (!w.is("td") && x.length > 0) {
                    w = x
                }
                if (w.is("td")) {
                    var z = w.data("date");
                    var y = 0;
                    if (z) {
                        y = w.attr("class").indexOf("disabled")
                    }
                    if (z && q.settings.onSelect && y < 0) {
                        q.settings.onSelect.call(q, z)
                    }
                }
            });
            v.on("swipeLeft", function () {
                l.setMonth(l.getMonth() + 1);
                q.refresh(l)
            });
            v.on("swipeRight", function () {
                l.setMonth(l.getMonth() - 1);
                q.refresh(l)
            })
        };
        this.refresh = function (z) {
            var A = new Date(u, p, 1), x = new Date(z.getFullYear(), z.getMonth(), 1), B = undefined, y;
            if (A.getTime() == x.getTime()) {
                return
            }
            B = A < x ? "slideLeftRound" : "slideRightRound";
            e.text(z.getFullYear());
            c.text(this.settings.months[z.getMonth()]);
            var w = m(z);
            J.anim(i, B, function () {
                i.html(w)
            })
        };
        k()
    };
    b.prototype.parse = function (d) {
        var c = /^(\d{4})(?:\-|\/)(\d{1,2})(?:\-|\/)(\d{1,2})$/;
        return c.test(d) ? new Date(parseInt(RegExp.$1, 10), parseInt(RegExp.$2, 10) - 1, parseInt(RegExp.$3, 10)) : null
    };
    b.prototype.format = function (e) {
        var g = e.getFullYear(), c = e.getMonth() + 1, f = e.getDate();
        c = (c < 10) ? ("0" + c) : c;
        f = (f < 10) ? ("0" + f) : f;
        return g + "-" + c + "-" + f
    };
    J.Calendar = b
})(J.$);
(function (c) {
    var a = {}, b = 1;
    J.Scroll = function (e, i) {
        var d, f, h = c(e), g = {
            hScroll: false,
            bounce: false,
            lockDirection: true,
            useTransform: true,
            useTransition: false,
            checkDOMChanges: false,
            onBeforeScrollStart: function (k) {
                var j = k.target;
                while (j.nodeType != 1) {
                    j = j.parentNode
                }
                if (j.tagName != "SELECT" && j.tagName != "INPUT" && j.tagName != "TEXTAREA") {
                    k.preventDefault()
                }
            }
        };
        f = h.data("_jscroll_");
        if (f && a[f]) {
            d = a[f];
            c.extend(d.scroller.options, i);
            d.scroller.refresh();
            return d
        } else {
            f = "_jscroll_" + b++;
            h.data("_jscroll_", f);
            c.extend(g, i);
            scroller = new iScroll(h[0], g);
            return a[f] = {
                scroller: scroller, destroy: function () {
                    scroller.destroy();
                    delete a[f]
                }
            }
        }
    }
})(J.$);
(function (b) {
    function a(s, n) {
        var x = function () {
        }, k = function () {
            return true
        }, u = false, g = 0, t = 200, f, h, m, p, d, e, w, r;
        var q = this;
        if (b.isPlainObject(s)) {
            f = b(s.selector);
            n = s.noDots;
            k = s.onBeforeSlide || k;
            x = s.onAfterSlide || x;
            r = s.autoPlay
        } else {
            f = b(s)
        }
        var i = function () {
            f.css("overflow", "hidden");
            m = f.children().first();
            p = m.children();
            d = p.length;
            e = f.offset().width;
            m.css("width", d * e);
            p.css({"width": e, "float": "left"});
            if (!n) {
                l()
            }
            o(0, 0);
            if (r) {
                v()
            }
        };
        var v = function () {
            setTimeout(function () {
                if (g == d - 1) {
                    o(0)
                } else {
                    q.next()
                }
                v()
            }, 3000)
        };
        var l = function () {
            h = f.find(".dots");
            if (h.length > 0) {
                h.show()
            } else {
                var C = d * 30 + 20 + 2;
                var B = '<div class="dots"><ul>';
                for (var A = 0; A < d; A++) {
                    B += '<li index="' + A + '"';
                    if (A == 0) {
                        B += 'class="active"'
                    }
                    B += '><a href="#"></a></li>'
                }
                B += "</ul></div>";
                f.append(B);
                h = f.find(".dots");
                h.children().css("width", C + "px");
                h.find("li").on("tap", function () {
                    var D = b(this).attr("index");
                    o(parseInt(D), t)
                })
            }
        };
        var o = function (A, B) {
            B = B || t;
            m.css({
                "-webkit-transition-duration": B + "ms",
                "-webkit-transform": "translate3D(" + -(A * e) + "px,0,0)"
            });
            if (g != A) {
                g = A;
                if (h) {
                    b(h.find("li").get(g)).addClass("active").siblings().removeClass("active")
                }
                x(g)
            }
        };
        var z = function () {
            m.on("touchstart", y, false);
            m.on("touchmove", c, false);
            m.on("touchend", j, false)
        };
        var y = function (A) {
            var B = A.touches[0];
            start = {pageX: B.pageX, pageY: B.pageY, time: Number(new Date())};
            isScrolling = undefined;
            w = 0;
            m[0].style.webkitTransitionDuration = 0;
            u = true
        };
        var c = function (B) {
            if (!u) {
                return
            }
            var C = B.touches[0];
            w = C.pageX - start.pageX;
            if (typeof isScrolling == "undefined") {
                isScrolling = Math.abs(w) < Math.abs(C.pageY - start.pageY)
            }
            if (!isScrolling) {
                B.preventDefault();
                var A = !g && w > 0 || g == d - 1 && w < 0;
                if (A) {
                    return
                }
                var D = (w - g * e);
                m[0].style.webkitTransform = "translate3D(" + D + "px,0,0)"
            }
        };
        var j = function (C) {
            var B = (Number(new Date()) - start.time < 250 && Math.abs(w) > 20) || Math.abs(w) > e / 3;
            var A = !g && w > 0 || g == d - 1 && w < 0;
            if (!isScrolling) {
                if (k(g, w)) {
                    o(g + (B && !A ? (w < 0 ? 1 : -1) : 0), t)
                } else {
                    o(g)
                }
            }
            u = false
        };
        i();
        z();
        this.refresh = function () {
            m.attr("style", "");
            i()
        };
        this.prev = function () {
            if (g) {
                o(g - 1, t)
            }
        };
        this.next = function () {
            if (g < d - 1) {
                o(g + 1, t)
            }
        };
        this.index = function (A) {
            o(A)
        }
    }

    J.Slider = a
})(J.$);
(function (d) {
    var a = {}, c = 1;

    function b(f, m, o) {
        var k, i, j, n, e, h, q, p = {
            selector: undefined,
            type: "pullDown",
            minPullHeight: 10,
            pullText: "下拉刷新...",
            releaseText: "松开立即刷新...",
            refreshText: "刷新中...",
            refreshTip: false,
            onPullIcon: "arrow-down-2",
            onReleaseIcon: "icon-reverse",
            onRefreshIcon: "spinner",
            callback: undefined
        };
        if (typeof f === "object") {
            d.extend(p, f)
        } else {
            p.selector = f;
            p.type = m;
            p.callback = o;
            if (m === "pullUp") {
                d.extend(p, {
                    pullText: "上拉加载更多...",
                    releaseText: "松开开立即加载...",
                    refreshText: "加载中...",
                    onPullIcon: "arrow-up-3"
                })
            }
        }
        q = p.type === "pullDown" ? true : false;
        var l = function (s) {
            i = d(s.selector).children()[0];
            var r = '<div class="refresh-container"><span class="refresh-icon icon ' + s.onPullIcon + '"></span><span class="refresh-label">' + s.pullText + "</span>" + (s.refreshTip ? '<div class="refresh-tip">' + s.refreshTip + "</div>" : "") + "</div>";
            if (q) {
                j = d(r).prependTo(i)
            } else {
                j = d(r).appendTo(i)
            }
            h = j.height();
            n = j.find(".refresh-icon");
            e = j.find(".refresh-label")
        };
        var g = function (r) {
            return J.Scroll(r.selector, {
                topOffset: q ? h : 0, bounce: true, onScrollMove: function () {
                    if (this.y > r.minPullHeight && q && !n.hasClass(r.onReleaseIcon)) {
                        n.addClass(r.onReleaseIcon);
                        e.html(r.releaseText);
                        this.minScrollY = 0
                    } else {
                        if (this.y < r.minPullHeight && q && n.hasClass(r.onReleaseIcon)) {
                            n.removeClass(r.onReleaseIcon);
                            e.html(r.pullText);
                            this.minScrollY = -h
                        } else {
                            if (this.y < (this.maxScrollY - r.minPullHeight) && !q && !n.hasClass(r.onReleaseIcon)) {
                                n.addClass(r.onReleaseIcon);
                                e.html(r.releaseText);
                                this.maxScrollY = this.maxScrollY
                            } else {
                                if (this.y > (this.maxScrollY + r.minPullHeight) && !q && n.hasClass(r.onReleaseIcon)) {
                                    n.removeClass(r.onReleaseIcon);
                                    e.html(r.pullText);
                                    this.maxScrollY = h
                                }
                            }
                        }
                    }
                }, onScrollEnd: function () {
                    if (n.hasClass(r.onReleaseIcon)) {
                        n.removeClass(r.onReleaseIcon).removeClass(r.onPullIcon).addClass(r.onRefreshIcon);
                        e.html(r.refreshText);
                        var s = this;
                        setTimeout(function () {
                            r.callback.call(s)
                        }, 1)
                    }
                }, onRefresh: function () {
                    n.removeClass(r.onRefreshIcon).addClass(r.onPullIcon);
                    e.html(r.pullText)
                }
            })
        };
        l(p);
        k = g(p);
        return k
    }

    J.Refresh = function (e, i, j) {
        var h, f;
        if (e.selector) {
            h = d(e.selector)
        } else {
            h = d(e)
        }
        f = h.data("_jrefresh_");
        if (f && a[f]) {
            return a[f]
        } else {
            f = "_jrefresh_" + c++;
            h.data("_jrefresh_", f);
            var g = new b(e, i, j);
            return a[f] = {
                scroller: g.scroller, destroy: function () {
                    delete a[f];
                    g.scroller.destroy();
                    d(".refresh-container", e).remove()
                }
            }
        }
    }
})(J.$);
var JSEncryptExports = {};
(function (ap) {
    var bE;
    var n = 244837814094590;
    var aV = ((n & 16777215) == 15715070);

    function bf(z, t, L) {
        if (z != null) {
            if ("number" == typeof z) {
                this.fromNumber(z, t, L)
            } else {
                if (t == null && "string" != typeof z) {
                    this.fromString(z, 256)
                } else {
                    this.fromString(z, t)
                }
            }
        }
    }

    function bm() {
        return new bf(null)
    }

    function a7(bX, t, z, bW, bZ, bY) {
        while (--bY >= 0) {
            var L = t * this[bX++] + z[bW] + bZ;
            bZ = Math.floor(L / 67108864);
            z[bW++] = L & 67108863
        }
        return bZ
    }

    function a6(bX, b2, b3, bW, b0, t) {
        var bZ = b2 & 32767, b1 = b2 >> 15;
        while (--t >= 0) {
            var L = this[bX] & 32767;
            var bY = this[bX++] >> 15;
            var z = b1 * L + bY * bZ;
            L = bZ * L + ((z & 32767) << 15) + b3[bW] + (b0 & 1073741823);
            b0 = (L >>> 30) + (z >>> 15) + b1 * bY + (b0 >>> 30);
            b3[bW++] = L & 1073741823
        }
        return b0
    }

    function a5(bX, b2, b3, bW, b0, t) {
        var bZ = b2 & 16383, b1 = b2 >> 14;
        while (--t >= 0) {
            var L = this[bX] & 16383;
            var bY = this[bX++] >> 14;
            var z = b1 * L + bY * bZ;
            L = bZ * L + ((z & 16383) << 14) + b3[bW] + b0;
            b0 = (L >> 28) + (z >> 14) + b1 * bY;
            b3[bW++] = L & 268435455
        }
        return b0
    }

    if (aV && (navigator.appName == "Microsoft Internet Explorer")) {
        bf.prototype.am = a6;
        bE = 30
    } else {
        if (aV && (navigator.appName != "Netscape")) {
            bf.prototype.am = a7;
            bE = 26
        } else {
            bf.prototype.am = a5;
            bE = 28
        }
    }
    bf.prototype.DB = bE;
    bf.prototype.DM = ((1 << bE) - 1);
    bf.prototype.DV = (1 << bE);
    var bQ = 52;
    bf.prototype.FV = Math.pow(2, bQ);
    bf.prototype.F1 = bQ - bE;
    bf.prototype.F2 = 2 * bE - bQ;
    var a = "0123456789abcdefghijklmnopqrstuvwxyz";
    var g = new Array();
    var aH, E;
    aH = "0".charCodeAt(0);
    for (E = 0; E <= 9; ++E) {
        g[aH++] = E
    }
    aH = "a".charCodeAt(0);
    for (E = 10; E < 36; ++E) {
        g[aH++] = E
    }
    aH = "A".charCodeAt(0);
    for (E = 10; E < 36; ++E) {
        g[aH++] = E
    }
    function Y(t) {
        return a.charAt(t)
    }

    function aX(z, t) {
        var L = g[z.charCodeAt(t)];
        return (L == null) ? -1 : L
    }

    function d(z) {
        for (var t = this.t - 1; t >= 0; --t) {
            z[t] = this[t]
        }
        z.t = this.t;
        z.s = this.s
    }

    function h(t) {
        this.t = 1;
        this.s = (t < 0) ? -1 : 0;
        if (t > 0) {
            this[0] = t
        } else {
            if (t < -1) {
                this[0] = t + this.DV
            } else {
                this.t = 0
            }
        }
    }

    function bi(t) {
        var z = bm();
        z.fromInt(t);
        return z
    }

    function bI(bZ, z) {
        var bW;
        if (z == 16) {
            bW = 4
        } else {
            if (z == 8) {
                bW = 3
            } else {
                if (z == 256) {
                    bW = 8
                } else {
                    if (z == 2) {
                        bW = 1
                    } else {
                        if (z == 32) {
                            bW = 5
                        } else {
                            if (z == 4) {
                                bW = 2
                            } else {
                                this.fromRadix(bZ, z);
                                return
                            }
                        }
                    }
                }
            }
        }
        this.t = 0;
        this.s = 0;
        var bY = bZ.length, L = false, bX = 0;
        while (--bY >= 0) {
            var t = (bW == 8) ? bZ[bY] & 255 : aX(bZ, bY);
            if (t < 0) {
                if (bZ.charAt(bY) == "-") {
                    L = true
                }
                continue
            }
            L = false;
            if (bX == 0) {
                this[this.t++] = t
            } else {
                if (bX + bW > this.DB) {
                    this[this.t - 1] |= (t & ((1 << (this.DB - bX)) - 1)) << bX;
                    this[this.t++] = (t >> (this.DB - bX))
                } else {
                    this[this.t - 1] |= t << bX
                }
            }
            bX += bW;
            if (bX >= this.DB) {
                bX -= this.DB
            }
        }
        if (bW == 8 && (bZ[0] & 128) != 0) {
            this.s = -1;
            if (bX > 0) {
                this[this.t - 1] |= ((1 << (this.DB - bX)) - 1) << bX
            }
        }
        this.clamp();
        if (L) {
            bf.ZERO.subTo(this, this)
        }
    }

    function bA() {
        var t = this.s & this.DM;
        while (this.t > 0 && this[this.t - 1] == t) {
            --this.t
        }
    }

    function u(z) {
        if (this.s < 0) {
            return "-" + this.negate().toString(z)
        }
        var L;
        if (z == 16) {
            L = 4
        } else {
            if (z == 8) {
                L = 3
            } else {
                if (z == 2) {
                    L = 1
                } else {
                    if (z == 32) {
                        L = 5
                    } else {
                        if (z == 4) {
                            L = 2
                        } else {
                            return this.toRadix(z)
                        }
                    }
                }
            }
        }
        var bX = (1 << L) - 1, b0, t = false, bY = "", bW = this.t;
        var bZ = this.DB - (bW * this.DB) % L;
        if (bW-- > 0) {
            if (bZ < this.DB && (b0 = this[bW] >> bZ) > 0) {
                t = true;
                bY = Y(b0)
            }
            while (bW >= 0) {
                if (bZ < L) {
                    b0 = (this[bW] & ((1 << bZ) - 1)) << (L - bZ);
                    b0 |= this[--bW] >> (bZ += this.DB - L)
                } else {
                    b0 = (this[bW] >> (bZ -= L)) & bX;
                    if (bZ <= 0) {
                        bZ += this.DB;
                        --bW
                    }
                }
                if (b0 > 0) {
                    t = true
                }
                if (t) {
                    bY += Y(b0)
                }
            }
        }
        return t ? bY : "0"
    }

    function bC() {
        var t = bm();
        bf.ZERO.subTo(this, t);
        return t
    }

    function bB() {
        return (this.s < 0) ? this.negate() : this
    }

    function bN(t) {
        var L = this.s - t.s;
        if (L != 0) {
            return L
        }
        var z = this.t;
        L = z - t.t;
        if (L != 0) {
            return (this.s < 0) ? -L : L
        }
        while (--z >= 0) {
            if ((L = this[z] - t[z]) != 0) {
                return L
            }
        }
        return 0
    }

    function q(z) {
        var bW = 1, L;
        if ((L = z >>> 16) != 0) {
            z = L;
            bW += 16
        }
        if ((L = z >> 8) != 0) {
            z = L;
            bW += 8
        }
        if ((L = z >> 4) != 0) {
            z = L;
            bW += 4
        }
        if ((L = z >> 2) != 0) {
            z = L;
            bW += 2
        }
        if ((L = z >> 1) != 0) {
            z = L;
            bW += 1
        }
        return bW
    }

    function bt() {
        if (this.t <= 0) {
            return 0
        }
        return this.DB * (this.t - 1) + q(this[this.t - 1] ^ (this.s & this.DM))
    }

    function bv(L, z) {
        var t;
        for (t = this.t - 1; t >= 0; --t) {
            z[t + L] = this[t]
        }
        for (t = L - 1; t >= 0; --t) {
            z[t] = 0
        }
        z.t = this.t + L;
        z.s = this.s
    }

    function a2(L, z) {
        for (var t = L; t < this.t; ++t) {
            z[t - L] = this[t]
        }
        z.t = Math.max(this.t - L, 0);
        z.s = this.s
    }

    function s(b0, bW) {
        var z = b0 % this.DB;
        var t = this.DB - z;
        var bY = (1 << t) - 1;
        var bX = Math.floor(b0 / this.DB), bZ = (this.s << z) & this.DM, L;
        for (L = this.t - 1; L >= 0; --L) {
            bW[L + bX + 1] = (this[L] >> t) | bZ;
            bZ = (this[L] & bY) << z
        }
        for (L = bX - 1; L >= 0; --L) {
            bW[L] = 0
        }
        bW[bX] = bZ;
        bW.t = this.t + bX + 1;
        bW.s = this.s;
        bW.clamp()
    }

    function bT(bZ, bW) {
        bW.s = this.s;
        var bX = Math.floor(bZ / this.DB);
        if (bX >= this.t) {
            bW.t = 0;
            return
        }
        var z = bZ % this.DB;
        var t = this.DB - z;
        var bY = (1 << z) - 1;
        bW[0] = this[bX] >> z;
        for (var L = bX + 1; L < this.t; ++L) {
            bW[L - bX - 1] |= (this[L] & bY) << t;
            bW[L - bX] = this[L] >> z
        }
        if (z > 0) {
            bW[this.t - bX - 1] |= (this.s & bY) << t
        }
        bW.t = this.t - bX;
        bW.clamp()
    }

    function bs(z, bW) {
        var L = 0, bX = 0, t = Math.min(z.t, this.t);
        while (L < t) {
            bX += this[L] - z[L];
            bW[L++] = bX & this.DM;
            bX >>= this.DB
        }
        if (z.t < this.t) {
            bX -= z.s;
            while (L < this.t) {
                bX += this[L];
                bW[L++] = bX & this.DM;
                bX >>= this.DB
            }
            bX += this.s
        } else {
            bX += this.s;
            while (L < z.t) {
                bX -= z[L];
                bW[L++] = bX & this.DM;
                bX >>= this.DB
            }
            bX -= z.s
        }
        bW.s = (bX < 0) ? -1 : 0;
        if (bX < -1) {
            bW[L++] = this.DV + bX
        } else {
            if (bX > 0) {
                bW[L++] = bX
            }
        }
        bW.t = L;
        bW.clamp()
    }

    function bJ(z, bW) {
        var t = this.abs(), bX = z.abs();
        var L = t.t;
        bW.t = L + bX.t;
        while (--L >= 0) {
            bW[L] = 0
        }
        for (L = 0; L < bX.t; ++L) {
            bW[L + t.t] = t.am(0, bX[L], bW, L, 0, t.t)
        }
        bW.s = 0;
        bW.clamp();
        if (this.s != z.s) {
            bf.ZERO.subTo(bW, bW)
        }
    }

    function au(L) {
        var t = this.abs();
        var z = L.t = 2 * t.t;
        while (--z >= 0) {
            L[z] = 0
        }
        for (z = 0; z < t.t - 1; ++z) {
            var bW = t.am(z, t[z], L, 2 * z, 0, 1);
            if ((L[z + t.t] += t.am(z + 1, 2 * t[z], L, 2 * z + 1, bW, t.t - z - 1)) >= t.DV) {
                L[z + t.t] -= t.DV;
                L[z + t.t + 1] = 1
            }
        }
        if (L.t > 0) {
            L[L.t - 1] += t.am(z, t[z], L, 2 * z, 0, 1)
        }
        L.s = 0;
        L.clamp()
    }

    function a9(b3, b0, bZ) {
        var b9 = b3.abs();
        if (b9.t <= 0) {
            return
        }
        var b1 = this.abs();
        if (b1.t < b9.t) {
            if (b0 != null) {
                b0.fromInt(0)
            }
            if (bZ != null) {
                this.copyTo(bZ)
            }
            return
        }
        if (bZ == null) {
            bZ = bm()
        }
        var bX = bm(), z = this.s, b2 = b3.s;
        var b8 = this.DB - q(b9[b9.t - 1]);
        if (b8 > 0) {
            b9.lShiftTo(b8, bX);
            b1.lShiftTo(b8, bZ)
        } else {
            b9.copyTo(bX);
            b1.copyTo(bZ)
        }
        var b5 = bX.t;
        var L = bX[b5 - 1];
        if (L == 0) {
            return
        }
        var b4 = L * (1 << this.F1) + ((b5 > 1) ? bX[b5 - 2] >> this.F2 : 0);
        var cc = this.FV / b4, cb = (1 << this.F1) / b4, ca = 1 << this.F2;
        var b7 = bZ.t, b6 = b7 - b5, bY = (b0 == null) ? bm() : b0;
        bX.dlShiftTo(b6, bY);
        if (bZ.compareTo(bY) >= 0) {
            bZ[bZ.t++] = 1;
            bZ.subTo(bY, bZ)
        }
        bf.ONE.dlShiftTo(b5, bY);
        bY.subTo(bX, bX);
        while (bX.t < b5) {
            bX[bX.t++] = 0
        }
        while (--b6 >= 0) {
            var bW = (bZ[--b7] == L) ? this.DM : Math.floor(bZ[b7] * cc + (bZ[b7 - 1] + ca) * cb);
            if ((bZ[b7] += bX.am(0, bW, bZ, b6, 0, b5)) < bW) {
                bX.dlShiftTo(b6, bY);
                bZ.subTo(bY, bZ);
                while (bZ[b7] < --bW) {
                    bZ.subTo(bY, bZ)
                }
            }
        }
        if (b0 != null) {
            bZ.drShiftTo(b5, b0);
            if (z != b2) {
                bf.ZERO.subTo(b0, b0)
            }
        }
        bZ.t = b5;
        bZ.clamp();
        if (b8 > 0) {
            bZ.rShiftTo(b8, bZ)
        }
        if (z < 0) {
            bf.ZERO.subTo(bZ, bZ)
        }
    }

    function bh(t) {
        var z = bm();
        this.abs().divRemTo(t, null, z);
        if (this.s < 0 && z.compareTo(bf.ZERO) > 0) {
            t.subTo(z, z)
        }
        return z
    }

    function aT(t) {
        this.m = t
    }

    function aI(t) {
        if (t.s < 0 || t.compareTo(this.m) >= 0) {
            return t.mod(this.m)
        } else {
            return t
        }
    }

    function c(t) {
        return t
    }

    function V(t) {
        t.divRemTo(this.m, null, t)
    }

    function p(t, L, z) {
        t.multiplyTo(L, z);
        this.reduce(z)
    }

    function aF(t, z) {
        t.squareTo(z);
        this.reduce(z)
    }

    aT.prototype.convert = aI;
    aT.prototype.revert = c;
    aT.prototype.reduce = V;
    aT.prototype.mulTo = p;
    aT.prototype.sqrTo = aF;
    function ab() {
        if (this.t < 1) {
            return 0
        }
        var t = this[0];
        if ((t & 1) == 0) {
            return 0
        }
        var z = t & 3;
        z = (z * (2 - (t & 15) * z)) & 15;
        z = (z * (2 - (t & 255) * z)) & 255;
        z = (z * (2 - (((t & 65535) * z) & 65535))) & 65535;
        z = (z * (2 - t * z % this.DV)) % this.DV;
        return (z > 0) ? this.DV - z : -z
    }

    function K(t) {
        this.m = t;
        this.mp = t.invDigit();
        this.mpl = this.mp & 32767;
        this.mph = this.mp >> 15;
        this.um = (1 << (t.DB - 15)) - 1;
        this.mt2 = 2 * t.t
    }

    function by(t) {
        var z = bm();
        t.abs().dlShiftTo(this.m.t, z);
        z.divRemTo(this.m, null, z);
        if (t.s < 0 && z.compareTo(bf.ZERO) > 0) {
            this.m.subTo(z, z)
        }
        return z
    }

    function bl(t) {
        var z = bm();
        t.copyTo(z);
        this.reduce(z);
        return z
    }

    function bV(t) {
        while (t.t <= this.mt2) {
            t[t.t++] = 0
        }
        for (var L = 0; L < this.m.t; ++L) {
            var z = t[L] & 32767;
            var bW = (z * this.mpl + (((z * this.mph + (t[L] >> 15) * this.mpl) & this.um) << 15)) & t.DM;
            z = L + this.m.t;
            t[z] += this.m.am(0, bW, t, L, 0, this.m.t);
            while (t[z] >= t.DV) {
                t[z] -= t.DV;
                t[++z]++
            }
        }
        t.clamp();
        t.drShiftTo(this.m.t, t);
        if (t.compareTo(this.m) >= 0) {
            t.subTo(this.m, t)
        }
    }

    function ac(t, z) {
        t.squareTo(z);
        this.reduce(z)
    }

    function bz(t, L, z) {
        t.multiplyTo(L, z);
        this.reduce(z)
    }

    K.prototype.convert = by;
    K.prototype.revert = bl;
    K.prototype.reduce = bV;
    K.prototype.mulTo = bz;
    K.prototype.sqrTo = ac;
    function ad() {
        return ((this.t > 0) ? (this[0] & 1) : this.s) == 0
    }

    function am(b0, b1) {
        if (b0 > 4294967295 || b0 < 1) {
            return bf.ONE
        }
        var bZ = bm(), L = bm(), bY = b1.convert(this), bX = q(b0) - 1;
        bY.copyTo(bZ);
        while (--bX >= 0) {
            b1.sqrTo(bZ, L);
            if ((b0 & (1 << bX)) > 0) {
                b1.mulTo(L, bY, bZ)
            } else {
                var bW = bZ;
                bZ = L;
                L = bW
            }
        }
        return b1.revert(bZ)
    }

    function aG(L, t) {
        var bW;
        if (L < 256 || t.isEven()) {
            bW = new aT(t)
        } else {
            bW = new K(t)
        }
        return this.exp(L, bW)
    }

    bf.prototype.copyTo = d;
    bf.prototype.fromInt = h;
    bf.prototype.fromString = bI;
    bf.prototype.clamp = bA;
    bf.prototype.dlShiftTo = bv;
    bf.prototype.drShiftTo = a2;
    bf.prototype.lShiftTo = s;
    bf.prototype.rShiftTo = bT;
    bf.prototype.subTo = bs;
    bf.prototype.multiplyTo = bJ;
    bf.prototype.squareTo = au;
    bf.prototype.divRemTo = a9;
    bf.prototype.invDigit = ab;
    bf.prototype.isEven = ad;
    bf.prototype.exp = am;
    bf.prototype.toString = u;
    bf.prototype.negate = bC;
    bf.prototype.abs = bB;
    bf.prototype.compareTo = bN;
    bf.prototype.bitLength = bt;
    bf.prototype.mod = bh;
    bf.prototype.modPowInt = aG;
    bf.ZERO = bi(0);
    bf.ONE = bi(1);
    function f() {
        var t = bm();
        this.copyTo(t);
        return t
    }

    function b() {
        if (this.s < 0) {
            if (this.t == 1) {
                return this[0] - this.DV
            } else {
                if (this.t == 0) {
                    return -1
                }
            }
        } else {
            if (this.t == 1) {
                return this[0]
            } else {
                if (this.t == 0) {
                    return 0
                }
            }
        }
        return ((this[1] & ((1 << (32 - this.DB)) - 1)) << this.DB) | this[0]
    }

    function bF() {
        return (this.t == 0) ? this.s : (this[0] << 24) >> 24
    }

    function ag() {
        return (this.t == 0) ? this.s : (this[0] << 16) >> 16
    }

    function aU(t) {
        return Math.floor(Math.LN2 * this.DB / Math.log(t))
    }

    function aZ() {
        if (this.s < 0) {
            return -1
        } else {
            if (this.t <= 0 || (this.t == 1 && this[0] <= 0)) {
                return 0
            } else {
                return 1
            }
        }
    }

    function I(t) {
        if (t == null) {
            t = 10
        }
        if (this.signum() == 0 || t < 2 || t > 36) {
            return "0"
        }
        var bW = this.chunkSize(t);
        var L = Math.pow(t, bW);
        var bZ = bi(L), b0 = bm(), bY = bm(), bX = "";
        this.divRemTo(bZ, b0, bY);
        while (b0.signum() > 0) {
            bX = (L + bY.intValue()).toString(t).substr(1) + bX;
            b0.divRemTo(bZ, b0, bY)
        }
        return bY.intValue().toString(t) + bX
    }

    function av(b1, bY) {
        this.fromInt(0);
        if (bY == null) {
            bY = 10
        }
        var bW = this.chunkSize(bY);
        var bX = Math.pow(bY, bW), L = false, t = 0, b0 = 0;
        for (var z = 0; z < b1.length; ++z) {
            var bZ = aX(b1, z);
            if (bZ < 0) {
                if (b1.charAt(z) == "-" && this.signum() == 0) {
                    L = true
                }
                continue
            }
            b0 = bY * b0 + bZ;
            if (++t >= bW) {
                this.dMultiply(bX);
                this.dAddOffset(b0, 0);
                t = 0;
                b0 = 0
            }
        }
        if (t > 0) {
            this.dMultiply(Math.pow(bY, t));
            this.dAddOffset(b0, 0)
        }
        if (L) {
            bf.ZERO.subTo(this, this)
        }
    }

    function aP(bW, L, bY) {
        if ("number" == typeof L) {
            if (bW < 2) {
                this.fromInt(1)
            } else {
                this.fromNumber(bW, bY);
                if (!this.testBit(bW - 1)) {
                    this.bitwiseTo(bf.ONE.shiftLeft(bW - 1), ak, this)
                }
                if (this.isEven()) {
                    this.dAddOffset(1, 0)
                }
                while (!this.isProbablePrime(L)) {
                    this.dAddOffset(2, 0);
                    if (this.bitLength() > bW) {
                        this.subTo(bf.ONE.shiftLeft(bW - 1), this)
                    }
                }
            }
        } else {
            var z = new Array(), bX = bW & 7;
            z.length = (bW >> 3) + 1;
            L.nextBytes(z);
            if (bX > 0) {
                z[0] &= ((1 << bX) - 1)
            } else {
                z[0] = 0
            }
            this.fromString(z, 256)
        }
    }

    function aK() {
        var z = this.t, L = new Array();
        L[0] = this.s;
        var bW = this.DB - (z * this.DB) % 8, bX, t = 0;
        if (z-- > 0) {
            if (bW < this.DB && (bX = this[z] >> bW) != (this.s & this.DM) >> bW) {
                L[t++] = bX | (this.s << (this.DB - bW))
            }
            while (z >= 0) {
                if (bW < 8) {
                    bX = (this[z] & ((1 << bW) - 1)) << (8 - bW);
                    bX |= this[--z] >> (bW += this.DB - 8)
                } else {
                    bX = (this[z] >> (bW -= 8)) & 255;
                    if (bW <= 0) {
                        bW += this.DB;
                        --z
                    }
                }
                if ((bX & 128) != 0) {
                    bX |= -256
                }
                if (t == 0 && (this.s & 128) != (bX & 128)) {
                    ++t
                }
                if (t > 0 || bX != this.s) {
                    L[t++] = bX
                }
            }
        }
        return L
    }

    function bG(t) {
        return (this.compareTo(t) == 0)
    }

    function W(t) {
        return (this.compareTo(t) < 0) ? this : t
    }

    function bu(t) {
        return (this.compareTo(t) > 0) ? this : t
    }

    function aJ(z, bY, bW) {
        var L, bX, t = Math.min(z.t, this.t);
        for (L = 0; L < t; ++L) {
            bW[L] = bY(this[L], z[L])
        }
        if (z.t < this.t) {
            bX = z.s & this.DM;
            for (L = t; L < this.t; ++L) {
                bW[L] = bY(this[L], bX)
            }
            bW.t = this.t
        } else {
            bX = this.s & this.DM;
            for (L = t; L < z.t; ++L) {
                bW[L] = bY(bX, z[L])
            }
            bW.t = z.t
        }
        bW.s = bY(this.s, z.s);
        bW.clamp()
    }

    function o(t, z) {
        return t & z
    }

    function bO(t) {
        var z = bm();
        this.bitwiseTo(t, o, z);
        return z
    }

    function ak(t, z) {
        return t | z
    }

    function aS(t) {
        var z = bm();
        this.bitwiseTo(t, ak, z);
        return z
    }

    function aa(t, z) {
        return t ^ z
    }

    function B(t) {
        var z = bm();
        this.bitwiseTo(t, aa, z);
        return z
    }

    function i(t, z) {
        return t & ~z
    }

    function aD(t) {
        var z = bm();
        this.bitwiseTo(t, i, z);
        return z
    }

    function T() {
        var z = bm();
        for (var t = 0; t < this.t; ++t) {
            z[t] = this.DM & ~this[t]
        }
        z.t = this.t;
        z.s = ~this.s;
        return z
    }

    function aN(z) {
        var t = bm();
        if (z < 0) {
            this.rShiftTo(-z, t)
        } else {
            this.lShiftTo(z, t)
        }
        return t
    }

    function R(z) {
        var t = bm();
        if (z < 0) {
            this.lShiftTo(-z, t)
        } else {
            this.rShiftTo(z, t)
        }
        return t
    }

    function bc(t) {
        if (t == 0) {
            return -1
        }
        var z = 0;
        if ((t & 65535) == 0) {
            t >>= 16;
            z += 16
        }
        if ((t & 255) == 0) {
            t >>= 8;
            z += 8
        }
        if ((t & 15) == 0) {
            t >>= 4;
            z += 4
        }
        if ((t & 3) == 0) {
            t >>= 2;
            z += 2
        }
        if ((t & 1) == 0) {
            ++z
        }
        return z
    }

    function aq() {
        for (var t = 0; t < this.t; ++t) {
            if (this[t] != 0) {
                return t * this.DB + bc(this[t])
            }
        }
        if (this.s < 0) {
            return this.t * this.DB
        }
        return -1
    }

    function bj(t) {
        var z = 0;
        while (t != 0) {
            t &= t - 1;
            ++z
        }
        return z
    }

    function ao() {
        var L = 0, t = this.s & this.DM;
        for (var z = 0; z < this.t; ++z) {
            L += bj(this[z] ^ t)
        }
        return L
    }

    function aL(z) {
        var t = Math.floor(z / this.DB);
        if (t >= this.t) {
            return (this.s != 0)
        }
        return ((this[t] & (1 << (z % this.DB))) != 0)
    }

    function U(L, z) {
        var t = bf.ONE.shiftLeft(L);
        this.bitwiseTo(t, z, t);
        return t
    }

    function a1(t) {
        return this.changeBit(t, ak)
    }

    function ah(t) {
        return this.changeBit(t, i)
    }

    function aO(t) {
        return this.changeBit(t, aa)
    }

    function S(z, bW) {
        var L = 0, bX = 0, t = Math.min(z.t, this.t);
        while (L < t) {
            bX += this[L] + z[L];
            bW[L++] = bX & this.DM;
            bX >>= this.DB
        }
        if (z.t < this.t) {
            bX += z.s;
            while (L < this.t) {
                bX += this[L];
                bW[L++] = bX & this.DM;
                bX >>= this.DB
            }
            bX += this.s
        } else {
            bX += this.s;
            while (L < z.t) {
                bX += z[L];
                bW[L++] = bX & this.DM;
                bX >>= this.DB
            }
            bX += z.s
        }
        bW.s = (bX < 0) ? -1 : 0;
        if (bX > 0) {
            bW[L++] = bX
        } else {
            if (bX < -1) {
                bW[L++] = this.DV + bX
            }
        }
        bW.t = L;
        bW.clamp()
    }

    function bg(t) {
        var z = bm();
        this.addTo(t, z);
        return z
    }

    function aA(t) {
        var z = bm();
        this.subTo(t, z);
        return z
    }

    function bH(t) {
        var z = bm();
        this.multiplyTo(t, z);
        return z
    }

    function bU() {
        var t = bm();
        this.squareTo(t);
        return t
    }

    function bd(t) {
        var z = bm();
        this.divRemTo(t, z, null);
        return z
    }

    function bP(t) {
        var z = bm();
        this.divRemTo(t, null, z);
        return z
    }

    function bk(t) {
        var L = bm(), z = bm();
        this.divRemTo(t, L, z);
        return new Array(L, z)
    }

    function e(t) {
        this[this.t] = this.am(0, t - 1, this, 0, 0, this.t);
        ++this.t;
        this.clamp()
    }

    function aR(z, t) {
        if (z == 0) {
            return
        }
        while (this.t <= t) {
            this[this.t++] = 0
        }
        this[t] += z;
        while (this[t] >= this.DV) {
            this[t] -= this.DV;
            if (++t >= this.t) {
                this[this.t++] = 0
            }
            ++this[t]
        }
    }

    function Z() {
    }

    function bw(t) {
        return t
    }

    function bK(t, L, z) {
        t.multiplyTo(L, z)
    }

    function ai(t, z) {
        t.squareTo(z)
    }

    Z.prototype.convert = bw;
    Z.prototype.revert = bw;
    Z.prototype.mulTo = bK;
    Z.prototype.sqrTo = ai;
    function Q(t) {
        return this.exp(t, new Z())
    }

    function aQ(t, bX, bW) {
        var L = Math.min(this.t + t.t, bX);
        bW.s = 0;
        bW.t = L;
        while (L > 0) {
            bW[--L] = 0
        }
        var z;
        for (z = bW.t - this.t; L < z; ++L) {
            bW[L + this.t] = this.am(0, t[L], bW, L, 0, this.t)
        }
        for (z = Math.min(t.t, bX); L < z; ++L) {
            this.am(0, t[L], bW, L, 0, bX - L)
        }
        bW.clamp()
    }

    function a0(t, bW, L) {
        --bW;
        var z = L.t = this.t + t.t - bW;
        L.s = 0;
        while (--z >= 0) {
            L[z] = 0
        }
        for (z = Math.max(bW - this.t, 0); z < t.t; ++z) {
            L[this.t + z - bW] = this.am(bW - z, t[z], L, 0, 0, this.t + z - bW)
        }
        L.clamp();
        L.drShiftTo(1, L)
    }

    function bR(t) {
        this.r2 = bm();
        this.q3 = bm();
        bf.ONE.dlShiftTo(2 * t.t, this.r2);
        this.mu = this.r2.divide(t);
        this.m = t
    }

    function H(t) {
        if (t.s < 0 || t.t > 2 * this.m.t) {
            return t.mod(this.m)
        } else {
            if (t.compareTo(this.m) < 0) {
                return t
            } else {
                var z = bm();
                t.copyTo(z);
                this.reduce(z);
                return z
            }
        }
    }

    function bM(t) {
        return t
    }

    function D(t) {
        t.drShiftTo(this.m.t - 1, this.r2);
        if (t.t > this.m.t + 1) {
            t.t = this.m.t + 1;
            t.clamp()
        }
        this.mu.multiplyUpperTo(this.r2, this.m.t + 1, this.q3);
        this.m.multiplyLowerTo(this.q3, this.m.t + 1, this.r2);
        while (t.compareTo(this.r2) < 0) {
            t.dAddOffset(1, this.m.t + 1)
        }
        t.subTo(this.r2, t);
        while (t.compareTo(this.m) >= 0) {
            t.subTo(this.m, t)
        }
    }

    function aM(t, z) {
        t.squareTo(z);
        this.reduce(z)
    }

    function x(t, L, z) {
        t.multiplyTo(L, z);
        this.reduce(z)
    }

    bR.prototype.convert = H;
    bR.prototype.revert = bM;
    bR.prototype.reduce = D;
    bR.prototype.mulTo = x;
    bR.prototype.sqrTo = aM;
    function N(b4, bZ) {
        var b2 = b4.bitLength(), b0, bW = bi(1), b7;
        if (b2 <= 0) {
            return bW
        } else {
            if (b2 < 18) {
                b0 = 1
            } else {
                if (b2 < 48) {
                    b0 = 3
                } else {
                    if (b2 < 144) {
                        b0 = 4
                    } else {
                        if (b2 < 768) {
                            b0 = 5
                        } else {
                            b0 = 6
                        }
                    }
                }
            }
        }
        if (b2 < 8) {
            b7 = new aT(bZ)
        } else {
            if (bZ.isEven()) {
                b7 = new bR(bZ)
            } else {
                b7 = new K(bZ)
            }
        }
        var b3 = new Array(), bY = 3, b5 = b0 - 1, L = (1 << b0) - 1;
        b3[1] = b7.convert(this);
        if (b0 > 1) {
            var ca = bm();
            b7.sqrTo(b3[1], ca);
            while (bY <= L) {
                b3[bY] = bm();
                b7.mulTo(ca, b3[bY - 2], b3[bY]);
                bY += 2
            }
        }
        var b1 = b4.t - 1, b8, b6 = true, bX = bm(), b9;
        b2 = q(b4[b1]) - 1;
        while (b1 >= 0) {
            if (b2 >= b5) {
                b8 = (b4[b1] >> (b2 - b5)) & L
            } else {
                b8 = (b4[b1] & ((1 << (b2 + 1)) - 1)) << (b5 - b2);
                if (b1 > 0) {
                    b8 |= b4[b1 - 1] >> (this.DB + b2 - b5)
                }
            }
            bY = b0;
            while ((b8 & 1) == 0) {
                b8 >>= 1;
                --bY
            }
            if ((b2 -= bY) < 0) {
                b2 += this.DB;
                --b1
            }
            if (b6) {
                b3[b8].copyTo(bW);
                b6 = false
            } else {
                while (bY > 1) {
                    b7.sqrTo(bW, bX);
                    b7.sqrTo(bX, bW);
                    bY -= 2
                }
                if (bY > 0) {
                    b7.sqrTo(bW, bX)
                } else {
                    b9 = bW;
                    bW = bX;
                    bX = b9
                }
                b7.mulTo(bX, b3[b8], bW)
            }
            while (b1 >= 0 && (b4[b1] & (1 << b2)) == 0) {
                b7.sqrTo(bW, bX);
                b9 = bW;
                bW = bX;
                bX = b9;
                if (--b2 < 0) {
                    b2 = this.DB - 1;
                    --b1
                }
            }
        }
        return b7.revert(bW)
    }

    function aB(L) {
        var z = (this.s < 0) ? this.negate() : this.clone();
        var bZ = (L.s < 0) ? L.negate() : L.clone();
        if (z.compareTo(bZ) < 0) {
            var bX = z;
            z = bZ;
            bZ = bX
        }
        var bW = z.getLowestSetBit(), bY = bZ.getLowestSetBit();
        if (bY < 0) {
            return z
        }
        if (bW < bY) {
            bY = bW
        }
        if (bY > 0) {
            z.rShiftTo(bY, z);
            bZ.rShiftTo(bY, bZ)
        }
        while (z.signum() > 0) {
            if ((bW = z.getLowestSetBit()) > 0) {
                z.rShiftTo(bW, z)
            }
            if ((bW = bZ.getLowestSetBit()) > 0) {
                bZ.rShiftTo(bW, bZ)
            }
            if (z.compareTo(bZ) >= 0) {
                z.subTo(bZ, z);
                z.rShiftTo(1, z)
            } else {
                bZ.subTo(z, bZ);
                bZ.rShiftTo(1, bZ)
            }
        }
        if (bY > 0) {
            bZ.lShiftTo(bY, bZ)
        }
        return bZ
    }

    function aj(bW) {
        if (bW <= 0) {
            return 0
        }
        var L = this.DV % bW, z = (this.s < 0) ? bW - 1 : 0;
        if (this.t > 0) {
            if (L == 0) {
                z = this[0] % bW
            } else {
                for (var t = this.t - 1; t >= 0; --t) {
                    z = (L * z + this[t]) % bW
                }
            }
        }
        return z
    }

    function bS(z) {
        var bY = z.isEven();
        if ((this.isEven() && bY) || z.signum() == 0) {
            return bf.ZERO
        }
        var bX = z.clone(), bW = this.clone();
        var L = bi(1), t = bi(0), b0 = bi(0), bZ = bi(1);
        while (bX.signum() != 0) {
            while (bX.isEven()) {
                bX.rShiftTo(1, bX);
                if (bY) {
                    if (!L.isEven() || !t.isEven()) {
                        L.addTo(this, L);
                        t.subTo(z, t)
                    }
                    L.rShiftTo(1, L)
                } else {
                    if (!t.isEven()) {
                        t.subTo(z, t)
                    }
                }
                t.rShiftTo(1, t)
            }
            while (bW.isEven()) {
                bW.rShiftTo(1, bW);
                if (bY) {
                    if (!b0.isEven() || !bZ.isEven()) {
                        b0.addTo(this, b0);
                        bZ.subTo(z, bZ)
                    }
                    b0.rShiftTo(1, b0)
                } else {
                    if (!bZ.isEven()) {
                        bZ.subTo(z, bZ)
                    }
                }
                bZ.rShiftTo(1, bZ)
            }
            if (bX.compareTo(bW) >= 0) {
                bX.subTo(bW, bX);
                if (bY) {
                    L.subTo(b0, L)
                }
                t.subTo(bZ, t)
            } else {
                bW.subTo(bX, bW);
                if (bY) {
                    b0.subTo(L, b0)
                }
                bZ.subTo(t, bZ)
            }
        }
        if (bW.compareTo(bf.ONE) != 0) {
            return bf.ZERO
        }
        if (bZ.compareTo(z) >= 0) {
            return bZ.subtract(z)
        }
        if (bZ.signum() < 0) {
            bZ.addTo(z, bZ)
        } else {
            return bZ
        }
        if (bZ.signum() < 0) {
            return bZ.add(z)
        } else {
            return bZ
        }
    }

    var az = [2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37, 41, 43, 47, 53, 59, 61, 67, 71, 73, 79, 83, 89, 97, 101, 103, 107, 109, 113, 127, 131, 137, 139, 149, 151, 157, 163, 167, 173, 179, 181, 191, 193, 197, 199, 211, 223, 227, 229, 233, 239, 241, 251, 257, 263, 269, 271, 277, 281, 283, 293, 307, 311, 313, 317, 331, 337, 347, 349, 353, 359, 367, 373, 379, 383, 389, 397, 401, 409, 419, 421, 431, 433, 439, 443, 449, 457, 461, 463, 467, 479, 487, 491, 499, 503, 509, 521, 523, 541, 547, 557, 563, 569, 571, 577, 587, 593, 599, 601, 607, 613, 617, 619, 631, 641, 643, 647, 653, 659, 661, 673, 677, 683, 691, 701, 709, 719, 727, 733, 739, 743, 751, 757, 761, 769, 773, 787, 797, 809, 811, 821, 823, 827, 829, 839, 853, 857, 859, 863, 877, 881, 883, 887, 907, 911, 919, 929, 937, 941, 947, 953, 967, 971, 977, 983, 991, 997];
    var k = (1 << 26) / az[az.length - 1];

    function bL(bY) {
        var bX, L = this.abs();
        if (L.t == 1 && L[0] <= az[az.length - 1]) {
            for (bX = 0; bX < az.length; ++bX) {
                if (L[0] == az[bX]) {
                    return true
                }
            }
            return false
        }
        if (L.isEven()) {
            return false
        }
        bX = 1;
        while (bX < az.length) {
            var z = az[bX], bW = bX + 1;
            while (bW < az.length && z < k) {
                z *= az[bW++]
            }
            z = L.modInt(z);
            while (bX < bW) {
                if (z % az[bX++] == 0) {
                    return false
                }
            }
        }
        return L.millerRabin(bY)
    }

    function aE(bY) {
        var bZ = this.subtract(bf.ONE);
        var L = bZ.getLowestSetBit();
        if (L <= 0) {
            return false
        }
        var b0 = bZ.shiftRight(L);
        bY = (bY + 1) >> 1;
        if (bY > az.length) {
            bY = az.length
        }
        var z = bm();
        for (var bX = 0; bX < bY; ++bX) {
            z.fromInt(az[Math.floor(Math.random() * az.length)]);
            var b1 = z.modPow(b0, this);
            if (b1.compareTo(bf.ONE) != 0 && b1.compareTo(bZ) != 0) {
                var bW = 1;
                while (bW++ < L && b1.compareTo(bZ) != 0) {
                    b1 = b1.modPowInt(2, this);
                    if (b1.compareTo(bf.ONE) == 0) {
                        return false
                    }
                }
                if (b1.compareTo(bZ) != 0) {
                    return false
                }
            }
        }
        return true
    }

    bf.prototype.chunkSize = aU;
    bf.prototype.toRadix = I;
    bf.prototype.fromRadix = av;
    bf.prototype.fromNumber = aP;
    bf.prototype.bitwiseTo = aJ;
    bf.prototype.changeBit = U;
    bf.prototype.addTo = S;
    bf.prototype.dMultiply = e;
    bf.prototype.dAddOffset = aR;
    bf.prototype.multiplyLowerTo = aQ;
    bf.prototype.multiplyUpperTo = a0;
    bf.prototype.modInt = aj;
    bf.prototype.millerRabin = aE;
    bf.prototype.clone = f;
    bf.prototype.intValue = b;
    bf.prototype.byteValue = bF;
    bf.prototype.shortValue = ag;
    bf.prototype.signum = aZ;
    bf.prototype.toByteArray = aK;
    bf.prototype.equals = bG;
    bf.prototype.min = W;
    bf.prototype.max = bu;
    bf.prototype.and = bO;
    bf.prototype.or = aS;
    bf.prototype.xor = B;
    bf.prototype.andNot = aD;
    bf.prototype.not = T;
    bf.prototype.shiftLeft = aN;
    bf.prototype.shiftRight = R;
    bf.prototype.getLowestSetBit = aq;
    bf.prototype.bitCount = ao;
    bf.prototype.testBit = aL;
    bf.prototype.setBit = a1;
    bf.prototype.clearBit = ah;
    bf.prototype.flipBit = aO;
    bf.prototype.add = bg;
    bf.prototype.subtract = aA;
    bf.prototype.multiply = bH;
    bf.prototype.divide = bd;
    bf.prototype.remainder = bP;
    bf.prototype.divideAndRemainder = bk;
    bf.prototype.modPow = N;
    bf.prototype.modInverse = bS;
    bf.prototype.pow = Q;
    bf.prototype.gcd = aB;
    bf.prototype.isProbablePrime = bL;
    bf.prototype.square = bU;
    function bp() {
        this.i = 0;
        this.j = 0;
        this.S = new Array()
    }

    function af(bX) {
        var bW, z, L;
        for (bW = 0; bW < 256; ++bW) {
            this.S[bW] = bW
        }
        z = 0;
        for (bW = 0; bW < 256; ++bW) {
            z = (z + this.S[bW] + bX[bW % bX.length]) & 255;
            L = this.S[bW];
            this.S[bW] = this.S[z];
            this.S[z] = L
        }
        this.i = 0;
        this.j = 0
    }

    function be() {
        var z;
        this.i = (this.i + 1) & 255;
        this.j = (this.j + this.S[this.i]) & 255;
        z = this.S[this.i];
        this.S[this.i] = this.S[this.j];
        this.S[this.j] = z;
        return this.S[(z + this.S[this.i]) & 255]
    }

    bp.prototype.init = af;
    bp.prototype.next = be;
    function P() {
        return new bp()
    }

    var y = 256;
    var j;
    var l;
    var C;
    if (l == null) {
        l = new Array();
        C = 0;
        var ba;
        if (window.crypto && window.crypto.getRandomValues) {
            var a8 = new Uint32Array(256);
            window.crypto.getRandomValues(a8);
            for (ba = 0; ba < a8.length; ++ba) {
                l[C++] = a8[ba] & 255
            }
        }
        var F = function (z) {
            this.count = this.count || 0;
            if (this.count >= 256 || C >= y) {
                if (window.removeEventListener) {
                    window.removeEventListener("mousemove", F)
                } else {
                    if (window.detachEvent) {
                        window.detachEvent("onmousemove", F)
                    }
                }
                return
            }
            this.count += 1;
            var t = z.x + z.y;
            l[C++] = t & 255
        };
        if (window.addEventListener) {
            window.addEventListener("mousemove", F)
        } else {
            if (window.attachEvent) {
                window.attachEvent("onmousemove", F)
            }
        }
    }
    function bb() {
        if (j == null) {
            j = P();
            while (C < y) {
                var t = Math.floor(65536 * Math.random());
                l[C++] = t & 255
            }
            j.init(l);
            for (C = 0; C < l.length; ++C) {
                l[C] = 0
            }
            C = 0
        }
        return j.next()
    }

    function aY(z) {
        var t;
        for (t = 0; t < z.length; ++t) {
            z[t] = bb()
        }
    }

    function G() {
    }

    G.prototype.nextBytes = aY;
    function w(z, t) {
        return new bf(z, t)
    }

    function m(L, bW) {
        var t = "";
        var z = 0;
        while (z + bW < L.length) {
            t += L.substring(z, z + bW) + "\n";
            z += bW
        }
        return t + L.substring(z, L.length)
    }

    function br(t) {
        if (t < 16) {
            return "0" + t.toString(16)
        } else {
            return t.toString(16)
        }
    }

    function bD(bW, bZ) {
        if (bZ < bW.length + 11) {
            console.error("Message too long for RSA");
            return null
        }
        var bY = new Array();
        var L = bW.length - 1;
        while (L >= 0 && bZ > 0) {
            var bX = bW.charCodeAt(L--);
            if (bX < 128) {
                bY[--bZ] = bX
            } else {
                if ((bX > 127) && (bX < 2048)) {
                    bY[--bZ] = (bX & 63) | 128;
                    bY[--bZ] = (bX >> 6) | 192
                } else {
                    bY[--bZ] = (bX & 63) | 128;
                    bY[--bZ] = ((bX >> 6) & 63) | 128;
                    bY[--bZ] = (bX >> 12) | 224
                }
            }
        }
        bY[--bZ] = 0;
        var z = new G();
        var t = new Array();
        while (bZ > 2) {
            t[0] = 0;
            while (t[0] == 0) {
                z.nextBytes(t)
            }
            bY[--bZ] = t[0]
        }
        bY[--bZ] = 2;
        bY[--bZ] = 0;
        return new bf(bY)
    }

    function A() {
        this.n = null;
        this.e = 0;
        this.d = null;
        this.p = null;
        this.q = null;
        this.dmp1 = null;
        this.dmq1 = null;
        this.coeff = null
    }

    function an(z, t) {
        if (z != null && t != null && z.length > 0 && t.length > 0) {
            this.n = w(z, 16);
            this.e = parseInt(t, 16)
        } else {
            console.error("Invalid RSA public key")
        }
    }

    function bq(t) {
        return t.modPowInt(this.e, this.n)
    }

    function al(L) {
        var t = bD(L, (this.n.bitLength() + 7) >> 3);
        if (t == null) {
            return null
        }
        var bW = this.doPublic(t);
        if (bW == null) {
            return null
        }
        var z = bW.toString(16);
        if ((z.length & 1) == 0) {
            return z
        } else {
            return "0" + z
        }
    }

    A.prototype.doPublic = bq;
    A.prototype.setPublic = an;
    A.prototype.encrypt = al;
    function bo(bW, bY) {
        var t = bW.toByteArray();
        var L = 0;
        while (L < t.length && t[L] == 0) {
            ++L
        }
        if (t.length - L != bY - 1 || t[L] != 2) {
            return null
        }
        ++L;
        while (t[L] != 0) {
            if (++L >= t.length) {
                return null
            }
        }
        var z = "";
        while (++L < t.length) {
            var bX = t[L] & 255;
            if (bX < 128) {
                z += String.fromCharCode(bX)
            } else {
                if ((bX > 191) && (bX < 224)) {
                    z += String.fromCharCode(((bX & 31) << 6) | (t[L + 1] & 63));
                    ++L
                } else {
                    z += String.fromCharCode(((bX & 15) << 12) | ((t[L + 1] & 63) << 6) | (t[L + 2] & 63));
                    L += 2
                }
            }
        }
        return z
    }

    function aC(L, t, z) {
        if (L != null && t != null && L.length > 0 && t.length > 0) {
            this.n = w(L, 16);
            this.e = parseInt(t, 16);
            this.d = w(z, 16)
        } else {
            console.error("Invalid RSA private key")
        }
    }

    function O(bZ, bW, bX, L, z, t, b0, bY) {
        if (bZ != null && bW != null && bZ.length > 0 && bW.length > 0) {
            this.n = w(bZ, 16);
            this.e = parseInt(bW, 16);
            this.d = w(bX, 16);
            this.p = w(L, 16);
            this.q = w(z, 16);
            this.dmp1 = w(t, 16);
            this.dmq1 = w(b0, 16);
            this.coeff = w(bY, 16)
        } else {
            console.error("Invalid RSA private key")
        }
    }

    function ax(L, b2) {
        var z = new G();
        var bZ = L >> 1;
        this.e = parseInt(b2, 16);
        var bW = new bf(b2, 16);
        for (; ;) {
            for (; ;) {
                this.p = new bf(L - bZ, 1, z);
                if (this.p.subtract(bf.ONE).gcd(bW).compareTo(bf.ONE) == 0 && this.p.isProbablePrime(10)) {
                    break
                }
            }
            for (; ;) {
                this.q = new bf(bZ, 1, z);
                if (this.q.subtract(bf.ONE).gcd(bW).compareTo(bf.ONE) == 0 && this.q.isProbablePrime(10)) {
                    break
                }
            }
            if (this.p.compareTo(this.q) <= 0) {
                var b1 = this.p;
                this.p = this.q;
                this.q = b1
            }
            var b0 = this.p.subtract(bf.ONE);
            var bX = this.q.subtract(bf.ONE);
            var bY = b0.multiply(bX);
            if (bY.gcd(bW).compareTo(bf.ONE) == 0) {
                this.n = this.p.multiply(this.q);
                this.d = bW.modInverse(bY);
                this.dmp1 = this.d.mod(b0);
                this.dmq1 = this.d.mod(bX);
                this.coeff = this.q.modInverse(this.p);
                break
            }
        }
    }

    function ay(t) {
        if (this.p == null || this.q == null) {
            return t.modPow(this.d, this.n)
        }
        var L = t.mod(this.p).modPow(this.dmp1, this.p);
        var z = t.mod(this.q).modPow(this.dmq1, this.q);
        while (L.compareTo(z) < 0) {
            L = L.add(this.p)
        }
        return L.subtract(z).multiply(this.coeff).mod(this.p).multiply(this.q).add(z)
    }

    function r(z) {
        var L = w(z, 16);
        var t = this.doPrivate(L);
        if (t == null) {
            return null
        }
        return bo(t, (this.n.bitLength() + 7) >> 3)
    }

    A.prototype.doPrivate = ay;
    A.prototype.setPrivate = aC;
    A.prototype.setPrivateEx = O;
    A.prototype.generate = ax;
    A.prototype.decrypt = r;
    (function () {
        var z = function (b3, b1, b2) {
            var bZ = new G();
            var bW = b3 >> 1;
            this.e = parseInt(b1, 16);
            var bY = new bf(b1, 16);
            var b0 = this;
            var bX = function () {
                var b5 = function () {
                    if (b0.p.compareTo(b0.q) <= 0) {
                        var b8 = b0.p;
                        b0.p = b0.q;
                        b0.q = b8
                    }
                    var ca = b0.p.subtract(bf.ONE);
                    var b7 = b0.q.subtract(bf.ONE);
                    var b9 = ca.multiply(b7);
                    if (b9.gcd(bY).compareTo(bf.ONE) == 0) {
                        b0.n = b0.p.multiply(b0.q);
                        b0.d = bY.modInverse(b9);
                        b0.dmp1 = b0.d.mod(ca);
                        b0.dmq1 = b0.d.mod(b7);
                        b0.coeff = b0.q.modInverse(b0.p);
                        setTimeout(function () {
                            b2()
                        }, 0)
                    } else {
                        setTimeout(bX, 0)
                    }
                };
                var b6 = function () {
                    b0.q = bm();
                    b0.q.fromNumberAsync(bW, 1, bZ, function () {
                        b0.q.subtract(bf.ONE).gcda(bY, function (b7) {
                            if (b7.compareTo(bf.ONE) == 0 && b0.q.isProbablePrime(10)) {
                                setTimeout(b5, 0)
                            } else {
                                setTimeout(b6, 0)
                            }
                        })
                    })
                };
                var b4 = function () {
                    b0.p = bm();
                    b0.p.fromNumberAsync(b3 - bW, 1, bZ, function () {
                        b0.p.subtract(bf.ONE).gcda(bY, function (b7) {
                            if (b7.compareTo(bf.ONE) == 0 && b0.p.isProbablePrime(10)) {
                                setTimeout(b6, 0)
                            } else {
                                setTimeout(b4, 0)
                            }
                        })
                    })
                };
                setTimeout(b4, 0)
            };
            setTimeout(bX, 0)
        };
        A.prototype.generateAsync = z;
        var t = function (bX, b3) {
            var bW = (this.s < 0) ? this.negate() : this.clone();
            var b2 = (bX.s < 0) ? bX.negate() : bX.clone();
            if (bW.compareTo(b2) < 0) {
                var bZ = bW;
                bW = b2;
                b2 = bZ
            }
            var bY = bW.getLowestSetBit(), b0 = b2.getLowestSetBit();
            if (b0 < 0) {
                b3(bW);
                return
            }
            if (bY < b0) {
                b0 = bY
            }
            if (b0 > 0) {
                bW.rShiftTo(b0, bW);
                b2.rShiftTo(b0, b2)
            }
            var b1 = function () {
                if ((bY = bW.getLowestSetBit()) > 0) {
                    bW.rShiftTo(bY, bW)
                }
                if ((bY = b2.getLowestSetBit()) > 0) {
                    b2.rShiftTo(bY, b2)
                }
                if (bW.compareTo(b2) >= 0) {
                    bW.subTo(b2, bW);
                    bW.rShiftTo(1, bW)
                } else {
                    b2.subTo(bW, b2);
                    b2.rShiftTo(1, b2)
                }
                if (!(bW.signum() > 0)) {
                    if (b0 > 0) {
                        b2.lShiftTo(b0, b2)
                    }
                    setTimeout(function () {
                        b3(b2)
                    }, 0)
                } else {
                    setTimeout(b1, 0)
                }
            };
            setTimeout(b1, 10)
        };
        bf.prototype.gcda = t;
        var L = function (b0, bX, b3, b2) {
            if ("number" == typeof bX) {
                if (b0 < 2) {
                    this.fromInt(1)
                } else {
                    this.fromNumber(b0, b3);
                    if (!this.testBit(b0 - 1)) {
                        this.bitwiseTo(bf.ONE.shiftLeft(b0 - 1), ak, this)
                    }
                    if (this.isEven()) {
                        this.dAddOffset(1, 0)
                    }
                    var bZ = this;
                    var bY = function () {
                        bZ.dAddOffset(2, 0);
                        if (bZ.bitLength() > b0) {
                            bZ.subTo(bf.ONE.shiftLeft(b0 - 1), bZ)
                        }
                        if (bZ.isProbablePrime(bX)) {
                            setTimeout(function () {
                                b2()
                            }, 0)
                        } else {
                            setTimeout(bY, 0)
                        }
                    };
                    setTimeout(bY, 0)
                }
            } else {
                var bW = new Array(), b1 = b0 & 7;
                bW.length = (b0 >> 3) + 1;
                bX.nextBytes(bW);
                if (b1 > 0) {
                    bW[0] &= ((1 << b1) - 1)
                } else {
                    bW[0] = 0
                }
                this.fromString(bW, 256)
            }
        };
        bf.prototype.fromNumberAsync = L
    })();
    var a4 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
    var J = "=";

    function ae(L) {
        var z;
        var bW;
        var t = "";
        for (z = 0; z + 3 <= L.length; z += 3) {
            bW = parseInt(L.substring(z, z + 3), 16);
            t += a4.charAt(bW >> 6) + a4.charAt(bW & 63)
        }
        if (z + 1 == L.length) {
            bW = parseInt(L.substring(z, z + 1), 16);
            t += a4.charAt(bW << 2)
        } else {
            if (z + 2 == L.length) {
                bW = parseInt(L.substring(z, z + 2), 16);
                t += a4.charAt(bW >> 2) + a4.charAt((bW & 3) << 4)
            }
        }
        while ((t.length & 3) > 0) {
            t += J
        }
        return t
    }

    function aW(bX) {
        var L = "";
        var bW;
        var t = 0;
        var z;
        for (bW = 0; bW < bX.length; ++bW) {
            if (bX.charAt(bW) == J) {
                break
            }
            v = a4.indexOf(bX.charAt(bW));
            if (v < 0) {
                continue
            }
            if (t == 0) {
                L += Y(v >> 2);
                z = v & 3;
                t = 1
            } else {
                if (t == 1) {
                    L += Y((z << 2) | (v >> 4));
                    z = v & 15;
                    t = 2
                } else {
                    if (t == 2) {
                        L += Y(z);
                        L += Y(v >> 2);
                        z = v & 3;
                        t = 3
                    } else {
                        L += Y((z << 2) | (v >> 4));
                        L += Y(v & 15);
                        t = 0
                    }
                }
            }
        }
        if (t == 1) {
            L += Y(z << 2)
        }
        return L
    }

    function M(bW) {
        var L = aW(bW);
        var z;
        var t = new Array();
        for (z = 0; 2 * z < L.length; ++z) {
            t[z] = parseInt(L.substring(2 * z, 2 * z + 2), 16)
        }
        return t
    }

    /*! asn1-1.0.2.js (c) 2013 Kenji Urushima | kjur.github.com/jsrsasign/license
     */
    var at = at || {};
    at.env = at.env || {};
    var bn = at, aw = Object.prototype, ar = "[object Function]", X = ["toString", "valueOf"];
    at.env.parseUA = function (bW) {
        var bX = function (b1) {
            var b2 = 0;
            return parseFloat(b1.replace(/\./g, function () {
                return (b2++ == 1) ? "" : "."
            }))
        }, b0 = navigator, bZ = {
            ie: 0,
            opera: 0,
            gecko: 0,
            webkit: 0,
            chrome: 0,
            mobile: null,
            air: 0,
            ipad: 0,
            iphone: 0,
            ipod: 0,
            ios: null,
            android: 0,
            webos: 0,
            caja: b0 && b0.cajaVersion,
            secure: false,
            os: null
        }, L = bW || (navigator && navigator.userAgent), bY = window && window.location, z = bY && bY.href, t;
        bZ.secure = z && (z.toLowerCase().indexOf("https") === 0);
        if (L) {
            if ((/windows|win32/i).test(L)) {
                bZ.os = "windows"
            } else {
                if ((/macintosh/i).test(L)) {
                    bZ.os = "macintosh"
                } else {
                    if ((/rhino/i).test(L)) {
                        bZ.os = "rhino"
                    }
                }
            }
            if ((/KHTML/).test(L)) {
                bZ.webkit = 1
            }
            t = L.match(/AppleWebKit\/([^\s]*)/);
            if (t && t[1]) {
                bZ.webkit = bX(t[1]);
                if (/ Mobile\//.test(L)) {
                    bZ.mobile = "Apple";
                    t = L.match(/OS ([^\s]*)/);
                    if (t && t[1]) {
                        t = bX(t[1].replace("_", "."))
                    }
                    bZ.ios = t;
                    bZ.ipad = bZ.ipod = bZ.iphone = 0;
                    t = L.match(/iPad|iPod|iPhone/);
                    if (t && t[0]) {
                        bZ[t[0].toLowerCase()] = bZ.ios
                    }
                } else {
                    t = L.match(/NokiaN[^\/]*|Android \d\.\d|webOS\/\d\.\d/);
                    if (t) {
                        bZ.mobile = t[0]
                    }
                    if (/webOS/.test(L)) {
                        bZ.mobile = "WebOS";
                        t = L.match(/webOS\/([^\s]*);/);
                        if (t && t[1]) {
                            bZ.webos = bX(t[1])
                        }
                    }
                    if (/ Android/.test(L)) {
                        bZ.mobile = "Android";
                        t = L.match(/Android ([^\s]*);/);
                        if (t && t[1]) {
                            bZ.android = bX(t[1])
                        }
                    }
                }
                t = L.match(/Chrome\/([^\s]*)/);
                if (t && t[1]) {
                    bZ.chrome = bX(t[1])
                } else {
                    t = L.match(/AdobeAIR\/([^\s]*)/);
                    if (t) {
                        bZ.air = t[0]
                    }
                }
            }
            if (!bZ.webkit) {
                t = L.match(/Opera[\s\/]([^\s]*)/);
                if (t && t[1]) {
                    bZ.opera = bX(t[1]);
                    t = L.match(/Version\/([^\s]*)/);
                    if (t && t[1]) {
                        bZ.opera = bX(t[1])
                    }
                    t = L.match(/Opera Mini[^;]*/);
                    if (t) {
                        bZ.mobile = t[0]
                    }
                } else {
                    t = L.match(/MSIE\s([^;]*)/);
                    if (t && t[1]) {
                        bZ.ie = bX(t[1])
                    } else {
                        t = L.match(/Gecko\/([^\s]*)/);
                        if (t) {
                            bZ.gecko = 1;
                            t = L.match(/rv:([^\s\)]*)/);
                            if (t && t[1]) {
                                bZ.gecko = bX(t[1])
                            }
                        }
                    }
                }
            }
        }
        return bZ
    };
    at.env.ua = at.env.parseUA();
    at.isFunction = function (t) {
        return (typeof t === "function") || aw.toString.apply(t) === ar
    };
    at._IEEnumFix = (at.env.ua.ie) ? function (L, z) {
        var t, bX, bW;
        for (t = 0; t < X.length; t = t + 1) {
            bX = X[t];
            bW = z[bX];
            if (bn.isFunction(bW) && bW != aw[bX]) {
                L[bX] = bW
            }
        }
    } : function () {
    };
    at.extend = function (bW, bX, L) {
        if (!bX || !bW) {
            throw new Error("extend failed, please check that " + "all dependencies are included.")
        }
        var z = function () {
        }, t;
        z.prototype = bX.prototype;
        bW.prototype = new z();
        bW.prototype.constructor = bW;
        bW.superclass = bX.prototype;
        if (bX.prototype.constructor == aw.constructor) {
            bX.prototype.constructor = bX
        }
        if (L) {
            for (t in L) {
                if (bn.hasOwnProperty(L, t)) {
                    bW.prototype[t] = L[t]
                }
            }
            bn._IEEnumFix(bW.prototype, L)
        }
    };
    if (typeof KJUR == "undefined" || !KJUR) {
        KJUR = {}
    }
    if (typeof KJUR.asn1 == "undefined" || !KJUR.asn1) {
        KJUR.asn1 = {}
    }
    KJUR.asn1.ASN1Util = new function () {
        this.integerToByteHex = function (t) {
            var z = t.toString(16);
            if ((z.length % 2) == 1) {
                z = "0" + z
            }
            return z
        };
        this.bigIntToMinTwosComplementsHex = function (b0) {
            var bY = b0.toString(16);
            if (bY.substr(0, 1) != "-") {
                if (bY.length % 2 == 1) {
                    bY = "0" + bY
                } else {
                    if (!bY.match(/^[0-7]/)) {
                        bY = "00" + bY
                    }
                }
            } else {
                var t = bY.substr(1);
                var bX = t.length;
                if (bX % 2 == 1) {
                    bX += 1
                } else {
                    if (!bY.match(/^[0-7]/)) {
                        bX += 2
                    }
                }
                var bZ = "";
                for (var bW = 0; bW < bX; bW++) {
                    bZ += "f"
                }
                var L = new bf(bZ, 16);
                var z = L.xor(b0).add(bf.ONE);
                bY = z.toString(16).replace(/^-/, "")
            }
            return bY
        };
        this.getPEMStringFromHex = function (t, z) {
            var bX = CryptoJS.enc.Hex.parse(t);
            var L = CryptoJS.enc.Base64.stringify(bX);
            var bW = L.replace(/(.{64})/g, "$1\r\n");
            bW = bW.replace(/\r\n$/, "");
            return "-----BEGIN " + z + "-----\r\n" + bW + "\r\n-----END " + z + "-----\r\n"
        }
    };
    KJUR.asn1.ASN1Object = function () {
        var L = true;
        var z = null;
        var bW = "00";
        var bX = "00";
        var t = "";
        this.getLengthHexFromValue = function () {
            if (typeof this.hV == "undefined" || this.hV == null) {
                throw"this.hV is null or undefined."
            }
            if (this.hV.length % 2 == 1) {
                throw"value hex must be even length: n=" + t.length + ",v=" + this.hV
            }
            var b1 = this.hV.length / 2;
            var b0 = b1.toString(16);
            if (b0.length % 2 == 1) {
                b0 = "0" + b0
            }
            if (b1 < 128) {
                return b0
            } else {
                var bZ = b0.length / 2;
                if (bZ > 15) {
                    throw"ASN.1 length too long to represent by 8x: n = " + b1.toString(16)
                }
                var bY = 128 + bZ;
                return bY.toString(16) + b0
            }
        };
        this.getEncodedHex = function () {
            if (this.hTLV == null || this.isModified) {
                this.hV = this.getFreshValueHex();
                this.hL = this.getLengthHexFromValue();
                this.hTLV = this.hT + this.hL + this.hV;
                this.isModified = false
            }
            return this.hTLV
        };
        this.getValueHex = function () {
            this.getEncodedHex();
            return this.hV
        };
        this.getFreshValueHex = function () {
            return ""
        }
    };
    KJUR.asn1.DERAbstractString = function (L) {
        KJUR.asn1.DERAbstractString.superclass.constructor.call(this);
        var z = null;
        var t = null;
        this.getString = function () {
            return this.s
        };
        this.setString = function (bW) {
            this.hTLV = null;
            this.isModified = true;
            this.s = bW;
            this.hV = stohex(this.s)
        };
        this.setStringHex = function (bW) {
            this.hTLV = null;
            this.isModified = true;
            this.s = null;
            this.hV = bW
        };
        this.getFreshValueHex = function () {
            return this.hV
        };
        if (typeof L != "undefined") {
            if (typeof L["str"] != "undefined") {
                this.setString(L["str"])
            } else {
                if (typeof L["hex"] != "undefined") {
                    this.setStringHex(L["hex"])
                }
            }
        }
    };
    at.extend(KJUR.asn1.DERAbstractString, KJUR.asn1.ASN1Object);
    KJUR.asn1.DERAbstractTime = function (L) {
        KJUR.asn1.DERAbstractTime.superclass.constructor.call(this);
        var z = null;
        var t = null;
        this.localDateToUTC = function (bX) {
            utc = bX.getTime() + (bX.getTimezoneOffset() * 60000);
            var bW = new Date(utc);
            return bW
        };
        this.formatDate = function (b1, b3) {
            var bW = this.zeroPadding;
            var b2 = this.localDateToUTC(b1);
            var b4 = String(b2.getFullYear());
            if (b3 == "utc") {
                b4 = b4.substr(2, 2)
            }
            var b0 = bW(String(b2.getMonth() + 1), 2);
            var b5 = bW(String(b2.getDate()), 2);
            var bX = bW(String(b2.getHours()), 2);
            var bY = bW(String(b2.getMinutes()), 2);
            var bZ = bW(String(b2.getSeconds()), 2);
            return b4 + b0 + b5 + bX + bY + bZ + "Z"
        };
        this.zeroPadding = function (bX, bW) {
            if (bX.length >= bW) {
                return bX
            }
            return new Array(bW - bX.length + 1).join("0") + bX
        };
        this.getString = function () {
            return this.s
        };
        this.setString = function (bW) {
            this.hTLV = null;
            this.isModified = true;
            this.s = bW;
            this.hV = stohex(this.s)
        };
        this.setByDateValue = function (b0, b2, bX, bW, bY, bZ) {
            var b1 = new Date(Date.UTC(b0, b2 - 1, bX, bW, bY, bZ, 0));
            this.setByDate(b1)
        };
        this.getFreshValueHex = function () {
            return this.hV
        }
    };
    at.extend(KJUR.asn1.DERAbstractTime, KJUR.asn1.ASN1Object);
    KJUR.asn1.DERAbstractStructured = function (z) {
        KJUR.asn1.DERAbstractString.superclass.constructor.call(this);
        var t = null;
        this.setByASN1ObjectArray = function (L) {
            this.hTLV = null;
            this.isModified = true;
            this.asn1Array = L
        };
        this.appendASN1Object = function (L) {
            this.hTLV = null;
            this.isModified = true;
            this.asn1Array.push(L)
        };
        this.asn1Array = new Array();
        if (typeof z != "undefined") {
            if (typeof z["array"] != "undefined") {
                this.asn1Array = z["array"]
            }
        }
    };
    at.extend(KJUR.asn1.DERAbstractStructured, KJUR.asn1.ASN1Object);
    KJUR.asn1.DERBoolean = function () {
        KJUR.asn1.DERBoolean.superclass.constructor.call(this);
        this.hT = "01";
        this.hTLV = "0101ff"
    };
    at.extend(KJUR.asn1.DERBoolean, KJUR.asn1.ASN1Object);
    KJUR.asn1.DERInteger = function (t) {
        KJUR.asn1.DERInteger.superclass.constructor.call(this);
        this.hT = "02";
        this.setByBigInteger = function (z) {
            this.hTLV = null;
            this.isModified = true;
            this.hV = KJUR.asn1.ASN1Util.bigIntToMinTwosComplementsHex(z)
        };
        this.setByInteger = function (L) {
            var z = new bf(String(L), 10);
            this.setByBigInteger(z)
        };
        this.setValueHex = function (z) {
            this.hV = z
        };
        this.getFreshValueHex = function () {
            return this.hV
        };
        if (typeof t != "undefined") {
            if (typeof t["bigint"] != "undefined") {
                this.setByBigInteger(t["bigint"])
            } else {
                if (typeof t["int"] != "undefined") {
                    this.setByInteger(t["int"])
                } else {
                    if (typeof t["hex"] != "undefined") {
                        this.setValueHex(t["hex"])
                    }
                }
            }
        }
    };
    at.extend(KJUR.asn1.DERInteger, KJUR.asn1.ASN1Object);
    KJUR.asn1.DERBitString = function (t) {
        KJUR.asn1.DERBitString.superclass.constructor.call(this);
        this.hT = "03";
        this.setHexValueIncludingUnusedBits = function (z) {
            this.hTLV = null;
            this.isModified = true;
            this.hV = z
        };
        this.setUnusedBitsAndHexValue = function (z, bW) {
            if (z < 0 || 7 < z) {
                throw"unused bits shall be from 0 to 7: u = " + z
            }
            var L = "0" + z;
            this.hTLV = null;
            this.isModified = true;
            this.hV = L + bW
        };
        this.setByBinaryString = function (bW) {
            bW = bW.replace(/0+$/, "");
            var bX = 8 - bW.length % 8;
            if (bX == 8) {
                bX = 0
            }
            for (var bY = 0; bY <= bX; bY++) {
                bW += "0"
            }
            var bZ = "";
            for (var bY = 0; bY < bW.length - 1; bY += 8) {
                var L = bW.substr(bY, 8);
                var z = parseInt(L, 2).toString(16);
                if (z.length == 1) {
                    z = "0" + z
                }
                bZ += z
            }
            this.hTLV = null;
            this.isModified = true;
            this.hV = "0" + bX + bZ
        };
        this.setByBooleanArray = function (bW) {
            var L = "";
            for (var z = 0; z < bW.length; z++) {
                if (bW[z] == true) {
                    L += "1"
                } else {
                    L += "0"
                }
            }
            this.setByBinaryString(L)
        };
        this.newFalseArray = function (bW) {
            var z = new Array(bW);
            for (var L = 0; L < bW; L++) {
                z[L] = false
            }
            return z
        };
        this.getFreshValueHex = function () {
            return this.hV
        };
        if (typeof t != "undefined") {
            if (typeof t["hex"] != "undefined") {
                this.setHexValueIncludingUnusedBits(t["hex"])
            } else {
                if (typeof t["bin"] != "undefined") {
                    this.setByBinaryString(t["bin"])
                } else {
                    if (typeof t["array"] != "undefined") {
                        this.setByBooleanArray(t["array"])
                    }
                }
            }
        }
    };
    at.extend(KJUR.asn1.DERBitString, KJUR.asn1.ASN1Object);
    KJUR.asn1.DEROctetString = function (t) {
        KJUR.asn1.DEROctetString.superclass.constructor.call(this, t);
        this.hT = "04"
    };
    at.extend(KJUR.asn1.DEROctetString, KJUR.asn1.DERAbstractString);
    KJUR.asn1.DERNull = function () {
        KJUR.asn1.DERNull.superclass.constructor.call(this);
        this.hT = "05";
        this.hTLV = "0500"
    };
    at.extend(KJUR.asn1.DERNull, KJUR.asn1.ASN1Object);
    KJUR.asn1.DERObjectIdentifier = function (L) {
        var z = function (bW) {
            var bX = bW.toString(16);
            if (bX.length == 1) {
                bX = "0" + bX
            }
            return bX
        };
        var t = function (b1) {
            var b0 = "";
            var bX = new bf(b1, 10);
            var bW = bX.toString(2);
            var bY = 7 - bW.length % 7;
            if (bY == 7) {
                bY = 0
            }
            var b3 = "";
            for (var bZ = 0; bZ < bY; bZ++) {
                b3 += "0"
            }
            bW = b3 + bW;
            for (var bZ = 0; bZ < bW.length - 1; bZ += 7) {
                var b2 = bW.substr(bZ, 7);
                if (bZ != bW.length - 7) {
                    b2 = "1" + b2
                }
                b0 += z(parseInt(b2, 2))
            }
            return b0
        };
        KJUR.asn1.DERObjectIdentifier.superclass.constructor.call(this);
        this.hT = "06";
        this.setValueHex = function (bW) {
            this.hTLV = null;
            this.isModified = true;
            this.s = null;
            this.hV = bW
        };
        this.setValueOidString = function (bY) {
            if (!bY.match(/^[0-9.]+$/)) {
                throw"malformed oid string: " + bY
            }
            var bZ = "";
            var bW = bY.split(".");
            var b0 = parseInt(bW[0]) * 40 + parseInt(bW[1]);
            bZ += z(b0);
            bW.splice(0, 2);
            for (var bX = 0; bX < bW.length; bX++) {
                bZ += t(bW[bX])
            }
            this.hTLV = null;
            this.isModified = true;
            this.s = null;
            this.hV = bZ
        };
        this.setValueName = function (bX) {
            if (typeof KJUR.asn1.x509.OID.name2oidList[bX] != "undefined") {
                var bW = KJUR.asn1.x509.OID.name2oidList[bX];
                this.setValueOidString(bW)
            } else {
                throw"DERObjectIdentifier oidName undefined: " + bX
            }
        };
        this.getFreshValueHex = function () {
            return this.hV
        };
        if (typeof L != "undefined") {
            if (typeof L["oid"] != "undefined") {
                this.setValueOidString(L["oid"])
            } else {
                if (typeof L["hex"] != "undefined") {
                    this.setValueHex(L["hex"])
                } else {
                    if (typeof L["name"] != "undefined") {
                        this.setValueName(L["name"])
                    }
                }
            }
        }
    };
    at.extend(KJUR.asn1.DERObjectIdentifier, KJUR.asn1.ASN1Object);
    KJUR.asn1.DERUTF8String = function (t) {
        KJUR.asn1.DERUTF8String.superclass.constructor.call(this, t);
        this.hT = "0c"
    };
    at.extend(KJUR.asn1.DERUTF8String, KJUR.asn1.DERAbstractString);
    KJUR.asn1.DERNumericString = function (t) {
        KJUR.asn1.DERNumericString.superclass.constructor.call(this, t);
        this.hT = "12"
    };
    at.extend(KJUR.asn1.DERNumericString, KJUR.asn1.DERAbstractString);
    KJUR.asn1.DERPrintableString = function (t) {
        KJUR.asn1.DERPrintableString.superclass.constructor.call(this, t);
        this.hT = "13"
    };
    at.extend(KJUR.asn1.DERPrintableString, KJUR.asn1.DERAbstractString);
    KJUR.asn1.DERTeletexString = function (t) {
        KJUR.asn1.DERTeletexString.superclass.constructor.call(this, t);
        this.hT = "14"
    };
    at.extend(KJUR.asn1.DERTeletexString, KJUR.asn1.DERAbstractString);
    KJUR.asn1.DERIA5String = function (t) {
        KJUR.asn1.DERIA5String.superclass.constructor.call(this, t);
        this.hT = "16"
    };
    at.extend(KJUR.asn1.DERIA5String, KJUR.asn1.DERAbstractString);
    KJUR.asn1.DERUTCTime = function (t) {
        KJUR.asn1.DERUTCTime.superclass.constructor.call(this, t);
        this.hT = "17";
        this.setByDate = function (z) {
            this.hTLV = null;
            this.isModified = true;
            this.date = z;
            this.s = this.formatDate(this.date, "utc");
            this.hV = stohex(this.s)
        };
        if (typeof t != "undefined") {
            if (typeof t["str"] != "undefined") {
                this.setString(t["str"])
            } else {
                if (typeof t["hex"] != "undefined") {
                    this.setStringHex(t["hex"])
                } else {
                    if (typeof t["date"] != "undefined") {
                        this.setByDate(t["date"])
                    }
                }
            }
        }
    };
    at.extend(KJUR.asn1.DERUTCTime, KJUR.asn1.DERAbstractTime);
    KJUR.asn1.DERGeneralizedTime = function (t) {
        KJUR.asn1.DERGeneralizedTime.superclass.constructor.call(this, t);
        this.hT = "18";
        this.setByDate = function (z) {
            this.hTLV = null;
            this.isModified = true;
            this.date = z;
            this.s = this.formatDate(this.date, "gen");
            this.hV = stohex(this.s)
        };
        if (typeof t != "undefined") {
            if (typeof t["str"] != "undefined") {
                this.setString(t["str"])
            } else {
                if (typeof t["hex"] != "undefined") {
                    this.setStringHex(t["hex"])
                } else {
                    if (typeof t["date"] != "undefined") {
                        this.setByDate(t["date"])
                    }
                }
            }
        }
    };
    at.extend(KJUR.asn1.DERGeneralizedTime, KJUR.asn1.DERAbstractTime);
    KJUR.asn1.DERSequence = function (t) {
        KJUR.asn1.DERSequence.superclass.constructor.call(this, t);
        this.hT = "30";
        this.getFreshValueHex = function () {
            var L = "";
            for (var z = 0; z < this.asn1Array.length; z++) {
                var bW = this.asn1Array[z];
                L += bW.getEncodedHex()
            }
            this.hV = L;
            return this.hV
        }
    };
    at.extend(KJUR.asn1.DERSequence, KJUR.asn1.DERAbstractStructured);
    KJUR.asn1.DERSet = function (t) {
        KJUR.asn1.DERSet.superclass.constructor.call(this, t);
        this.hT = "31";
        this.getFreshValueHex = function () {
            var z = new Array();
            for (var L = 0; L < this.asn1Array.length; L++) {
                var bW = this.asn1Array[L];
                z.push(bW.getEncodedHex())
            }
            z.sort();
            this.hV = z.join("");
            return this.hV
        }
    };
    at.extend(KJUR.asn1.DERSet, KJUR.asn1.DERAbstractStructured);
    KJUR.asn1.DERTaggedObject = function (t) {
        KJUR.asn1.DERTaggedObject.superclass.constructor.call(this);
        this.hT = "a0";
        this.hV = "";
        this.isExplicit = true;
        this.asn1Object = null;
        this.setASN1Object = function (z, L, bW) {
            this.hT = L;
            this.isExplicit = z;
            this.asn1Object = bW;
            if (this.isExplicit) {
                this.hV = this.asn1Object.getEncodedHex();
                this.hTLV = null;
                this.isModified = true
            } else {
                this.hV = null;
                this.hTLV = bW.getEncodedHex();
                this.hTLV = this.hTLV.replace(/^../, L);
                this.isModified = false
            }
        };
        this.getFreshValueHex = function () {
            return this.hV
        };
        if (typeof t != "undefined") {
            if (typeof t["tag"] != "undefined") {
                this.hT = t["tag"]
            }
            if (typeof t["explicit"] != "undefined") {
                this.isExplicit = t["explicit"]
            }
            if (typeof t["obj"] != "undefined") {
                this.asn1Object = t["obj"];
                this.setASN1Object(this.isExplicit, this.hT, this.asn1Object)
            }
        }
    };
    at.extend(KJUR.asn1.DERTaggedObject, KJUR.asn1.ASN1Object);
    (function (z) {
        var t = {}, L;
        t.decode = function (bW) {
            var bY;
            if (L === z) {
                var bZ = "0123456789ABCDEF", b3 = " \f\n\r\t\u00A0\u2028\u2029";
                L = [];
                for (bY = 0; bY < 16; ++bY) {
                    L[bZ.charAt(bY)] = bY
                }
                bZ = bZ.toLowerCase();
                for (bY = 10; bY < 16; ++bY) {
                    L[bZ.charAt(bY)] = bY
                }
                for (bY = 0; bY < b3.length; ++bY) {
                    L[b3.charAt(bY)] = -1
                }
            }
            var bX = [], b0 = 0, b2 = 0;
            for (bY = 0; bY < bW.length; ++bY) {
                var b1 = bW.charAt(bY);
                if (b1 == "=") {
                    break
                }
                b1 = L[b1];
                if (b1 == -1) {
                    continue
                }
                if (b1 === z) {
                    throw"Illegal character at offset " + bY
                }
                b0 |= b1;
                if (++b2 >= 2) {
                    bX[bX.length] = b0;
                    b0 = 0;
                    b2 = 0
                } else {
                    b0 <<= 4
                }
            }
            if (b2) {
                throw"Hex encoding incomplete: 4 bits missing"
            }
            return bX
        };
        window.Hex = t
    })();
    (function (z) {
        var t = {}, L;
        t.decode = function (bW) {
            var bZ;
            if (L === z) {
                var bY = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/", b3 = "= \f\n\r\t\u00A0\u2028\u2029";
                L = [];
                for (bZ = 0; bZ < 64; ++bZ) {
                    L[bY.charAt(bZ)] = bZ
                }
                for (bZ = 0; bZ < b3.length; ++bZ) {
                    L[b3.charAt(bZ)] = -1
                }
            }
            var bX = [];
            var b0 = 0, b2 = 0;
            for (bZ = 0; bZ < bW.length; ++bZ) {
                var b1 = bW.charAt(bZ);
                if (b1 == "=") {
                    break
                }
                b1 = L[b1];
                if (b1 == -1) {
                    continue
                }
                if (b1 === z) {
                    throw"Illegal character at offset " + bZ
                }
                b0 |= b1;
                if (++b2 >= 4) {
                    bX[bX.length] = (b0 >> 16);
                    bX[bX.length] = (b0 >> 8) & 255;
                    bX[bX.length] = b0 & 255;
                    b0 = 0;
                    b2 = 0
                } else {
                    b0 <<= 6
                }
            }
            switch (b2) {
                case 1:
                    throw"Base64 encoding incomplete: at least 2 bits missing";
                case 2:
                    bX[bX.length] = (b0 >> 10);
                    break;
                case 3:
                    bX[bX.length] = (b0 >> 16);
                    bX[bX.length] = (b0 >> 8) & 255;
                    break
            }
            return bX
        };
        t.re = /-----BEGIN [^-]+-----([A-Za-z0-9+\/=\s]+)-----END [^-]+-----|begin-base64[^\n]+\n([A-Za-z0-9+\/=\s]+)====/;
        t.unarmor = function (bX) {
            var bW = t.re.exec(bX);
            if (bW) {
                if (bW[1]) {
                    bX = bW[1]
                } else {
                    if (bW[2]) {
                        bX = bW[2]
                    } else {
                        throw"RegExp out of sync"
                    }
                }
            }
            return t.decode(bX)
        };
        window.Base64 = t
    })();
    (function (bY) {
        var z = 100, t = "\u2026", L = {
            tag: function (b0, b1) {
                var bZ = document.createElement(b0);
                bZ.className = b1;
                return bZ
            }, text: function (bZ) {
                return document.createTextNode(bZ)
            }
        };

        function bX(bZ, b0) {
            if (bZ instanceof bX) {
                this.enc = bZ.enc;
                this.pos = bZ.pos
            } else {
                this.enc = bZ;
                this.pos = b0
            }
        }

        bX.prototype.get = function (bZ) {
            if (bZ === bY) {
                bZ = this.pos++
            }
            if (bZ >= this.enc.length) {
                throw"Requesting byte offset " + bZ + " on a stream of length " + this.enc.length
            }
            return this.enc[bZ]
        };
        bX.prototype.hexDigits = "0123456789ABCDEF";
        bX.prototype.hexByte = function (bZ) {
            return this.hexDigits.charAt((bZ >> 4) & 15) + this.hexDigits.charAt(bZ & 15)
        };
        bX.prototype.hexDump = function (b3, bZ, b0) {
            var b2 = "";
            for (var b1 = b3; b1 < bZ; ++b1) {
                b2 += this.hexByte(this.get(b1));
                if (b0 !== true) {
                    switch (b1 & 15) {
                        case 7:
                            b2 += "  ";
                            break;
                        case 15:
                            b2 += "\n";
                            break;
                        default:
                            b2 += " "
                    }
                }
            }
            return b2
        };
        bX.prototype.parseStringISO = function (b2, bZ) {
            var b1 = "";
            for (var b0 = b2; b0 < bZ; ++b0) {
                b1 += String.fromCharCode(this.get(b0))
            }
            return b1
        };
        bX.prototype.parseStringUTF = function (b3, bZ) {
            var b1 = "";
            for (var b0 = b3; b0 < bZ;) {
                var b2 = this.get(b0++);
                if (b2 < 128) {
                    b1 += String.fromCharCode(b2)
                } else {
                    if ((b2 > 191) && (b2 < 224)) {
                        b1 += String.fromCharCode(((b2 & 31) << 6) | (this.get(b0++) & 63))
                    } else {
                        b1 += String.fromCharCode(((b2 & 15) << 12) | ((this.get(b0++) & 63) << 6) | (this.get(b0++) & 63))
                    }
                }
            }
            return b1
        };
        bX.prototype.parseStringBMP = function (b4, b0) {
            var b3 = "";
            for (var b2 = b4; b2 < b0; b2 += 2) {
                var bZ = this.get(b2);
                var b1 = this.get(b2 + 1);
                b3 += String.fromCharCode((bZ << 8) + b1)
            }
            return b3
        };
        bX.prototype.reTime = /^((?:1[89]|2\d)?\d\d)(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])([01]\d|2[0-3])(?:([0-5]\d)(?:([0-5]\d)(?:[.,](\d{1,3}))?)?)?(Z|[-+](?:[0]\d|1[0-2])([0-5]\d)?)?$/;
        bX.prototype.parseTime = function (b2, b0) {
            var b1 = this.parseStringISO(b2, b0), bZ = this.reTime.exec(b1);
            if (!bZ) {
                return "Unrecognized time: " + b1
            }
            b1 = bZ[1] + "-" + bZ[2] + "-" + bZ[3] + " " + bZ[4];
            if (bZ[5]) {
                b1 += ":" + bZ[5];
                if (bZ[6]) {
                    b1 += ":" + bZ[6];
                    if (bZ[7]) {
                        b1 += "." + bZ[7]
                    }
                }
            }
            if (bZ[8]) {
                b1 += " UTC";
                if (bZ[8] != "Z") {
                    b1 += bZ[8];
                    if (bZ[9]) {
                        b1 += ":" + bZ[9]
                    }
                }
            }
            return b1
        };
        bX.prototype.parseInteger = function (b4, b0) {
            var bZ = b0 - b4;
            if (bZ > 4) {
                bZ <<= 3;
                var b2 = this.get(b4);
                if (b2 === 0) {
                    bZ -= 8
                } else {
                    while (b2 < 128) {
                        b2 <<= 1;
                        --bZ
                    }
                }
                return "(" + bZ + " bit)"
            }
            var b3 = 0;
            for (var b1 = b4; b1 < b0; ++b1) {
                b3 = (b3 << 8) | this.get(b1)
            }
            return b3
        };
        bX.prototype.parseBitString = function (bZ, b0) {
            var b4 = this.get(bZ), b2 = ((b0 - bZ - 1) << 3) - b4, b7 = "(" + b2 + " bit)";
            if (b2 <= 20) {
                var b6 = b4;
                b7 += " ";
                for (var b3 = b0 - 1; b3 > bZ; --b3) {
                    var b5 = this.get(b3);
                    for (var b1 = b6; b1 < 8; ++b1) {
                        b7 += (b5 >> b1) & 1 ? "1" : "0"
                    }
                    b6 = 0
                }
            }
            return b7
        };
        bX.prototype.parseOctetString = function (b3, b0) {
            var bZ = b0 - b3, b2 = "(" + bZ + " byte) ";
            if (bZ > z) {
                b0 = b3 + z
            }
            for (var b1 = b3; b1 < b0; ++b1) {
                b2 += this.hexByte(this.get(b1))
            }
            if (bZ > z) {
                b2 += t
            }
            return b2
        };
        bX.prototype.parseOID = function (b6, b0) {
            var b3 = "", b5 = 0, b4 = 0;
            for (var b2 = b6; b2 < b0; ++b2) {
                var b1 = this.get(b2);
                b5 = (b5 << 7) | (b1 & 127);
                b4 += 7;
                if (!(b1 & 128)) {
                    if (b3 === "") {
                        var bZ = b5 < 80 ? b5 < 40 ? 0 : 1 : 2;
                        b3 = bZ + "." + (b5 - bZ * 40)
                    } else {
                        b3 += "." + ((b4 >= 31) ? "bigint" : b5)
                    }
                    b5 = b4 = 0
                }
            }
            return b3
        };
        function bW(b2, b3, b1, bZ, b0) {
            this.stream = b2;
            this.header = b3;
            this.length = b1;
            this.tag = bZ;
            this.sub = b0
        }

        bW.prototype.typeName = function () {
            if (this.tag === bY) {
                return "unknown"
            }
            var b1 = this.tag >> 6, bZ = (this.tag >> 5) & 1, b0 = this.tag & 31;
            switch (b1) {
                case 0:
                    switch (b0) {
                        case 0:
                            return "EOC";
                        case 1:
                            return "BOOLEAN";
                        case 2:
                            return "INTEGER";
                        case 3:
                            return "BIT_STRING";
                        case 4:
                            return "OCTET_STRING";
                        case 5:
                            return "NULL";
                        case 6:
                            return "OBJECT_IDENTIFIER";
                        case 7:
                            return "ObjectDescriptor";
                        case 8:
                            return "EXTERNAL";
                        case 9:
                            return "REAL";
                        case 10:
                            return "ENUMERATED";
                        case 11:
                            return "EMBEDDED_PDV";
                        case 12:
                            return "UTF8String";
                        case 16:
                            return "SEQUENCE";
                        case 17:
                            return "SET";
                        case 18:
                            return "NumericString";
                        case 19:
                            return "PrintableString";
                        case 20:
                            return "TeletexString";
                        case 21:
                            return "VideotexString";
                        case 22:
                            return "IA5String";
                        case 23:
                            return "UTCTime";
                        case 24:
                            return "GeneralizedTime";
                        case 25:
                            return "GraphicString";
                        case 26:
                            return "VisibleString";
                        case 27:
                            return "GeneralString";
                        case 28:
                            return "UniversalString";
                        case 30:
                            return "BMPString";
                        default:
                            return "Universal_" + b0.toString(16)
                    }
                case 1:
                    return "Application_" + b0.toString(16);
                case 2:
                    return "[" + b0 + "]";
                case 3:
                    return "Private_" + b0.toString(16)
            }
        };
        bW.prototype.reSeemsASCII = /^[ -~]+$/;
        bW.prototype.content = function () {
            if (this.tag === bY) {
                return null
            }
            var b3 = this.tag >> 6, b0 = this.tag & 31, b2 = this.posContent(), bZ = Math.abs(this.length);
            if (b3 !== 0) {
                if (this.sub !== null) {
                    return "(" + this.sub.length + " elem)"
                }
                var b1 = this.stream.parseStringISO(b2, b2 + Math.min(bZ, z));
                if (this.reSeemsASCII.test(b1)) {
                    return b1.substring(0, 2 * z) + ((b1.length > 2 * z) ? t : "")
                } else {
                    return this.stream.parseOctetString(b2, b2 + bZ)
                }
            }
            switch (b0) {
                case 1:
                    return (this.stream.get(b2) === 0) ? "false" : "true";
                case 2:
                    return this.stream.parseInteger(b2, b2 + bZ);
                case 3:
                    return this.sub ? "(" + this.sub.length + " elem)" : this.stream.parseBitString(b2, b2 + bZ);
                case 4:
                    return this.sub ? "(" + this.sub.length + " elem)" : this.stream.parseOctetString(b2, b2 + bZ);
                case 6:
                    return this.stream.parseOID(b2, b2 + bZ);
                case 16:
                case 17:
                    return "(" + this.sub.length + " elem)";
                case 12:
                    return this.stream.parseStringUTF(b2, b2 + bZ);
                case 18:
                case 19:
                case 20:
                case 21:
                case 22:
                case 26:
                    return this.stream.parseStringISO(b2, b2 + bZ);
                case 30:
                    return this.stream.parseStringBMP(b2, b2 + bZ);
                case 23:
                case 24:
                    return this.stream.parseTime(b2, b2 + bZ)
            }
            return null
        };
        bW.prototype.toString = function () {
            return this.typeName() + "@" + this.stream.pos + "[header:" + this.header + ",length:" + this.length + ",sub:" + ((this.sub === null) ? "null" : this.sub.length) + "]"
        };
        bW.prototype.print = function (b0) {
            if (b0 === bY) {
                b0 = ""
            }
            document.writeln(b0 + this);
            if (this.sub !== null) {
                b0 += "  ";
                for (var b1 = 0, bZ = this.sub.length; b1 < bZ; ++b1) {
                    this.sub[b1].print(b0)
                }
            }
        };
        bW.prototype.toPrettyString = function (b0) {
            if (b0 === bY) {
                b0 = ""
            }
            var b2 = b0 + this.typeName() + " @" + this.stream.pos;
            if (this.length >= 0) {
                b2 += "+"
            }
            b2 += this.length;
            if (this.tag & 32) {
                b2 += " (constructed)"
            } else {
                if (((this.tag == 3) || (this.tag == 4)) && (this.sub !== null)) {
                    b2 += " (encapsulates)"
                }
            }
            b2 += "\n";
            if (this.sub !== null) {
                b0 += "  ";
                for (var b1 = 0, bZ = this.sub.length; b1 < bZ; ++b1) {
                    b2 += this.sub[b1].toPrettyString(b0)
                }
            }
            return b2
        };
        bW.prototype.toDOM = function () {
            var b0 = L.tag("div", "node");
            b0.asn1 = this;
            var b6 = L.tag("div", "head");
            var b8 = this.typeName().replace(/_/g, " ");
            b6.innerHTML = b8;
            var b4 = this.content();
            if (b4 !== null) {
                b4 = String(b4).replace(/</g, "&lt;");
                var b3 = L.tag("span", "preview");
                b3.appendChild(L.text(b4));
                b6.appendChild(b3)
            }
            b0.appendChild(b6);
            this.node = b0;
            this.head = b6;
            var b7 = L.tag("div", "value");
            b8 = "Offset: " + this.stream.pos + "<br/>";
            b8 += "Length: " + this.header + "+";
            if (this.length >= 0) {
                b8 += this.length
            } else {
                b8 += (-this.length) + " (undefined)"
            }
            if (this.tag & 32) {
                b8 += "<br/>(constructed)"
            } else {
                if (((this.tag == 3) || (this.tag == 4)) && (this.sub !== null)) {
                    b8 += "<br/>(encapsulates)"
                }
            }
            if (b4 !== null) {
                b8 += "<br/>Value:<br/><b>" + b4 + "</b>";
                if ((typeof oids === "object") && (this.tag == 6)) {
                    var b1 = oids[b4];
                    if (b1) {
                        if (b1.d) {
                            b8 += "<br/>" + b1.d
                        }
                        if (b1.c) {
                            b8 += "<br/>" + b1.c
                        }
                        if (b1.w) {
                            b8 += "<br/>(warning!)"
                        }
                    }
                }
            }
            b7.innerHTML = b8;
            b0.appendChild(b7);
            var bZ = L.tag("div", "sub");
            if (this.sub !== null) {
                for (var b2 = 0, b5 = this.sub.length; b2 < b5; ++b2) {
                    bZ.appendChild(this.sub[b2].toDOM())
                }
            }
            b0.appendChild(bZ);
            b6.onclick = function () {
                b0.className = (b0.className == "node collapsed") ? "node" : "node collapsed"
            };
            return b0
        };
        bW.prototype.posStart = function () {
            return this.stream.pos
        };
        bW.prototype.posContent = function () {
            return this.stream.pos + this.header
        };
        bW.prototype.posEnd = function () {
            return this.stream.pos + this.header + Math.abs(this.length)
        };
        bW.prototype.fakeHover = function (bZ) {
            this.node.className += " hover";
            if (bZ) {
                this.head.className += " hover"
            }
        };
        bW.prototype.fakeOut = function (b0) {
            var bZ = / ?hover/;
            this.node.className = this.node.className.replace(bZ, "");
            if (b0) {
                this.head.className = this.head.className.replace(bZ, "")
            }
        };
        bW.prototype.toHexDOM_sub = function (b2, b1, b3, b4, bZ) {
            if (b4 >= bZ) {
                return
            }
            var b0 = L.tag("span", b1);
            b0.appendChild(L.text(b3.hexDump(b4, bZ)));
            b2.appendChild(b0)
        };
        bW.prototype.toHexDOM = function (b0) {
            var b3 = L.tag("span", "hex");
            if (b0 === bY) {
                b0 = b3
            }
            this.head.hexNode = b3;
            this.head.onmouseover = function () {
                this.hexNode.className = "hexCurrent"
            };
            this.head.onmouseout = function () {
                this.hexNode.className = "hex"
            };
            b3.asn1 = this;
            b3.onmouseover = function () {
                var b5 = !b0.selected;
                if (b5) {
                    b0.selected = this.asn1;
                    this.className = "hexCurrent"
                }
                this.asn1.fakeHover(b5)
            };
            b3.onmouseout = function () {
                var b5 = (b0.selected == this.asn1);
                this.asn1.fakeOut(b5);
                if (b5) {
                    b0.selected = null;
                    this.className = "hex"
                }
            };
            this.toHexDOM_sub(b3, "tag", this.stream, this.posStart(), this.posStart() + 1);
            this.toHexDOM_sub(b3, (this.length >= 0) ? "dlen" : "ulen", this.stream, this.posStart() + 1, this.posContent());
            if (this.sub === null) {
                b3.appendChild(L.text(this.stream.hexDump(this.posContent(), this.posEnd())))
            } else {
                if (this.sub.length > 0) {
                    var b4 = this.sub[0];
                    var b2 = this.sub[this.sub.length - 1];
                    this.toHexDOM_sub(b3, "intro", this.stream, this.posContent(), b4.posStart());
                    for (var b1 = 0, bZ = this.sub.length; b1 < bZ; ++b1) {
                        b3.appendChild(this.sub[b1].toHexDOM(b0))
                    }
                    this.toHexDOM_sub(b3, "outro", this.stream, b2.posEnd(), this.posEnd())
                }
            }
            return b3
        };
        bW.prototype.toHexString = function (bZ) {
            return this.stream.hexDump(this.posStart(), this.posEnd(), true)
        };
        bW.decodeLength = function (b2) {
            var b0 = b2.get(), bZ = b0 & 127;
            if (bZ == b0) {
                return bZ
            }
            if (bZ > 3) {
                throw"Length over 24 bits not supported at position " + (b2.pos - 1)
            }
            if (bZ === 0) {
                return -1
            }
            b0 = 0;
            for (var b1 = 0; b1 < bZ; ++b1) {
                b0 = (b0 << 8) | b2.get()
            }
            return b0
        };
        bW.hasContent = function (b0, bZ, b5) {
            if (b0 & 32) {
                return true
            }
            if ((b0 < 3) || (b0 > 4)) {
                return false
            }
            var b4 = new bX(b5);
            if (b0 == 3) {
                b4.get()
            }
            var b3 = b4.get();
            if ((b3 >> 6) & 1) {
                return false
            }
            try {
                var b2 = bW.decodeLength(b4);
                return ((b4.pos - b5.pos) + b2 == bZ)
            } catch (b1) {
                return false
            }
        };
        bW.decode = function (b6) {
            if (!(b6 instanceof bX)) {
                b6 = new bX(b6, 0)
            }
            var b5 = new bX(b6), b8 = b6.get(), b3 = bW.decodeLength(b6), b2 = b6.pos - b5.pos, bZ = null;
            if (bW.hasContent(b8, b3, b6)) {
                var b0 = b6.pos;
                if (b8 == 3) {
                    b6.get()
                }
                bZ = [];
                if (b3 >= 0) {
                    var b1 = b0 + b3;
                    while (b6.pos < b1) {
                        bZ[bZ.length] = bW.decode(b6)
                    }
                    if (b6.pos != b1) {
                        throw"Content size is not correct for container starting at offset " + b0
                    }
                } else {
                    try {
                        for (; ;) {
                            var b7 = bW.decode(b6);
                            if (b7.tag === 0) {
                                break
                            }
                            bZ[bZ.length] = b7
                        }
                        b3 = b0 - b6.pos
                    } catch (b4) {
                        throw"Exception while decoding undefined length content: " + b4
                    }
                }
            } else {
                b6.pos += b3
            }
            return new bW(b5, b2, b3, b8, bZ)
        };
        bW.test = function () {
            var b4 = [{value: [39], expected: 39}, {value: [129, 201], expected: 201}, {
                value: [131, 254, 220, 186],
                expected: 16702650
            }];
            for (var b1 = 0, bZ = b4.length; b1 < bZ; ++b1) {
                var b3 = 0, b2 = new bX(b4[b1].value, 0), b0 = bW.decodeLength(b2);
                if (b0 != b4[b1].expected) {
                    document.write("In test[" + b1 + "] expected " + b4[b1].expected + " got " + b0 + "\n")
                }
            }
        };
        window.ASN1 = bW
    })();
    ASN1.prototype.getHexStringValue = function () {
        var t = this.toHexString();
        var L = this.header * 2;
        var z = this.length * 2;
        return t.substr(L, z)
    };
    A.prototype.parseKey = function (b1) {
        try {
            var b6 = 0;
            var bW = 0;
            var t = /^\s*(?:[0-9A-Fa-f][0-9A-Fa-f]\s*)+$/;
            var b5 = t.test(b1) ? Hex.decode(b1) : Base64.unarmor(b1);
            var bX = ASN1.decode(b5);
            if (bX.sub.length === 3) {
                bX = bX.sub[2].sub[0]
            }
            if (bX.sub.length === 9) {
                b6 = bX.sub[1].getHexStringValue();
                this.n = w(b6, 16);
                bW = bX.sub[2].getHexStringValue();
                this.e = parseInt(bW, 16);
                var z = bX.sub[3].getHexStringValue();
                this.d = w(z, 16);
                var b0 = bX.sub[4].getHexStringValue();
                this.p = w(b0, 16);
                var bZ = bX.sub[5].getHexStringValue();
                this.q = w(bZ, 16);
                var b3 = bX.sub[6].getHexStringValue();
                this.dmp1 = w(b3, 16);
                var b2 = bX.sub[7].getHexStringValue();
                this.dmq1 = w(b2, 16);
                var L = bX.sub[8].getHexStringValue();
                this.coeff = w(L, 16)
            } else {
                if (bX.sub.length === 2) {
                    var b7 = bX.sub[1];
                    var bY = b7.sub[0];
                    b6 = bY.sub[0].getHexStringValue();
                    this.n = w(b6, 16);
                    bW = bY.sub[1].getHexStringValue();
                    this.e = parseInt(bW, 16)
                } else {
                    return false
                }
            }
            return true
        } catch (b4) {
            return false
        }
    };
    A.prototype.getPrivateBaseKey = function () {
        var z = {"array": [new KJUR.asn1.DERInteger({"int": 0}), new KJUR.asn1.DERInteger({"bigint": this.n}), new KJUR.asn1.DERInteger({"int": this.e}), new KJUR.asn1.DERInteger({"bigint": this.d}), new KJUR.asn1.DERInteger({"bigint": this.p}), new KJUR.asn1.DERInteger({"bigint": this.q}), new KJUR.asn1.DERInteger({"bigint": this.dmp1}), new KJUR.asn1.DERInteger({"bigint": this.dmq1}), new KJUR.asn1.DERInteger({"bigint": this.coeff})]};
        var t = new KJUR.asn1.DERSequence(z);
        return t.getEncodedHex()
    };
    A.prototype.getPrivateBaseKeyB64 = function () {
        return ae(this.getPrivateBaseKey())
    };
    A.prototype.getPublicBaseKey = function () {
        var L = {"array": [new KJUR.asn1.DERObjectIdentifier({"oid": "1.2.840.113549.1.1.1"}), new KJUR.asn1.DERNull()]};
        var t = new KJUR.asn1.DERSequence(L);
        L = {"array": [new KJUR.asn1.DERInteger({"bigint": this.n}), new KJUR.asn1.DERInteger({"int": this.e})]};
        var bX = new KJUR.asn1.DERSequence(L);
        L = {"hex": "00" + bX.getEncodedHex()};
        var bW = new KJUR.asn1.DERBitString(L);
        L = {"array": [t, bW]};
        var z = new KJUR.asn1.DERSequence(L);
        return z.getEncodedHex()
    };
    A.prototype.getPublicBaseKeyB64 = function () {
        return ae(this.getPublicBaseKey())
    };
    A.prototype.wordwrap = function (L, t) {
        t = t || 64;
        if (!L) {
            return L
        }
        var z = "(.{1," + t + "})( +|$\n?)|(.{1," + t + "})";
        return L.match(RegExp(z, "g")).join("\n")
    };
    A.prototype.getPrivateKey = function () {
        var t = "-----BEGIN RSA PRIVATE KEY-----\n";
        t += this.wordwrap(this.getPrivateBaseKeyB64()) + "\n";
        t += "-----END RSA PRIVATE KEY-----";
        return t
    };
    A.prototype.getPublicKey = function () {
        var t = "-----BEGIN PUBLIC KEY-----\n";
        t += this.wordwrap(this.getPublicBaseKeyB64()) + "\n";
        t += "-----END PUBLIC KEY-----";
        return t
    };
    A.prototype.hasPublicKeyProperty = function (t) {
        t = t || {};
        return (t.hasOwnProperty("n") && t.hasOwnProperty("e"))
    };
    A.prototype.hasPrivateKeyProperty = function (t) {
        t = t || {};
        return (t.hasOwnProperty("n") && t.hasOwnProperty("e") && t.hasOwnProperty("d") && t.hasOwnProperty("p") && t.hasOwnProperty("q") && t.hasOwnProperty("dmp1") && t.hasOwnProperty("dmq1") && t.hasOwnProperty("coeff"))
    };
    A.prototype.parsePropertiesFrom = function (t) {
        this.n = t.n;
        this.e = t.e;
        if (t.hasOwnProperty("d")) {
            this.d = t.d;
            this.p = t.p;
            this.q = t.q;
            this.dmp1 = t.dmp1;
            this.dmq1 = t.dmq1;
            this.coeff = t.coeff
        }
    };
    var bx = function (t) {
        A.call(this);
        if (t) {
            if (typeof t === "string") {
                this.parseKey(t)
            } else {
                if (this.hasPrivateKeyProperty(t) || this.hasPublicKeyProperty(t)) {
                    this.parsePropertiesFrom(t)
                }
            }
        }
    };
    bx.prototype = new A();
    bx.prototype.constructor = bx;
    var a3 = function (t) {
        t = t || {};
        this.default_key_size = parseInt(t.default_key_size) || 1024;
        this.default_public_exponent = t.default_public_exponent || "010001";
        this.log = t.log || false;
        this.key = null
    };
    a3.prototype.setKey = function (t) {
        if (this.log && this.key) {
            console.warn("A key was already set, overriding existing.")
        }
        this.key = new bx(t)
    };
    a3.prototype.setPrivateKey = function (t) {
        this.setKey(t)
    };
    a3.prototype.setPublicKey = function (t) {
        this.setKey(t)
    };
    a3.prototype.decrypt = function (t) {
        try {
            return this.getKey().decrypt(aW(t))
        } catch (z) {
            return false
        }
    };
    a3.prototype.encrypt = function (t) {
        try {
            return ae(this.getKey().encrypt(t))
        } catch (z) {
            return false
        }
    };
    a3.prototype.getKey = function (t) {
        if (!this.key) {
            this.key = new bx();
            if (t && {}.toString.call(t) === "[object Function]") {
                this.key.generateAsync(this.default_key_size, this.default_public_exponent, t);
                return
            }
            this.key.generate(this.default_key_size, this.default_public_exponent)
        }
        return this.key
    };
    a3.prototype.getPrivateKey = function () {
        return this.getKey().getPrivateKey()
    };
    a3.prototype.getPrivateKeyB64 = function () {
        return this.getKey().getPrivateBaseKeyB64()
    };
    a3.prototype.getPublicKey = function () {
        return this.getKey().getPublicKey()
    };
    a3.prototype.getPublicKeyB64 = function () {
        return this.getKey().getPublicBaseKeyB64()
    };
    ap.JSEncrypt = a3
})(JSEncryptExports);
var JSEncrypt = JSEncryptExports.JSEncrypt;
document.addEventListener("deviceready", onDeviceReady, false);
function onDeviceReady() {
    navigator.splashscreen.hide();
    document.addEventListener("backbutton", function (b) {
        if (J.hasMenuOpen) {
            J.Menu.hide()
        } else {
            if (J.hasPopupOpen) {
                J.closePopup()
            } else {
                var a = $("section.active").attr("id");
                if (a == "index_section") {
                    J.confirm("提示", "是否退出程序？", function () {
                        navigator.app.exitApp()
                    })
                } else {
                    window.history.go(-1)
                }
            }
        }
    }, false)
}
var App = (function () {
    var a = {};
    var e = function () {
        $.each(a, function (h, g) {
            var i = "#" + h + "_section";
            $("body").delegate(i, "pageinit", function () {
                g.init && g.init.call(g)
            });
            $("body").delegate(i, "pageshow", function (k, j) {
                g.show && g.show.call(g);
                if (!j && g.load) {
                    g.load.call(g)
                }
            })
        });
        J.Transition.add("flip", "slideLeftOut", "flipOut", "slideRightOut", "flipIn");
        Jingle.launch({})
    };
    var d = function (h, g) {
        return ((h && g) ? c : f).call(this, h, g)
    };
    var c = function (h, g) {
        a[h] = new g()
    };
    var f = function (g) {
        return a[g]
    };
    var b = function () {
        return {height: $(document).height() - 44 - 30 - 60, width: $(document).width()}
    };
    return {run: e, page: d, calcChartOffset: b}
}());
$(function () {
    App.run()
});