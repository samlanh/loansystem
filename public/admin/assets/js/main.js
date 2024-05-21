"use strict";

document.getElementById("layout-menu") && (isHorizontalLayout = document.getElementById("layout-menu").classList.contains("menu-horizontal")),
function() {
    setTimeout(function() {
        window.Helpers.initCustomOptionCheck();
		if(window.Helpers.isDarkStyle()){
			$("div.app-brand" ).addClass( "dark-mode" );
		}else{
			$("div.app-brand" ).removeClass( "dark-mode" );
		}
    }, 1e3),
    "undefined" != typeof Waves && (Waves.init(),
    Waves.attach(".btn[class*='btn-']:not([class*='btn-outline-']):not([class*='btn-label-'])", ["waves-light"]),
    Waves.attach("[class*='btn-outline-']"),
    Waves.attach("[class*='btn-label-']"),
    Waves.attach(".pagination .page-item .page-link")),
    document.querySelectorAll("#layout-menu").forEach(function(e) {
        menu = new Menu(e,{
            orientation: isHorizontalLayout ? "horizontal" : "vertical",
            closeChildren: !!isHorizontalLayout,
            showDropdownOnHover: localStorage.getItem("templateCustomizer-" + templateName + "--ShowDropdownOnHover") ? "true" === localStorage.getItem("templateCustomizer-" + templateName + "--ShowDropdownOnHover") : void 0 === window.templateCustomizer || window.templateCustomizer.settings.defaultShowDropdownOnHover
        }),
        window.Helpers.scrollToActive(animate = !1),
        window.Helpers.mainMenu = menu
    }),
    document.querySelectorAll(".layout-menu-toggle").forEach(e=>{
        e.addEventListener("click", e=>{
            if (e.preventDefault(),
            window.Helpers.toggleCollapsed(),
            config.enableMenuLocalStorage && !window.Helpers.isSmallScreen())
                try {
                    localStorage.setItem("templateCustomizer-" + templateName + "--LayoutCollapsed", String(window.Helpers.isCollapsed()));
                    var t, a = document.querySelector(".template-customizer-layouts-options");
                    a && (t = window.Helpers.isCollapsed() ? "collapsed" : "expanded",
                    a.querySelector(`input[value="${t}"]`).click())
                } catch (e) {}
        }
        )
    }
    ),
    window.Helpers.swipeIn(".drag-target", function(e) {
        window.Helpers.setCollapsed(!1)
    }),
    window.Helpers.swipeOut("#layout-menu", function(e) {
        window.Helpers.isSmallScreen() && window.Helpers.setCollapsed(!0)
    });
    let e = document.getElementsByClassName("menu-inner")
      , t = document.getElementsByClassName("menu-inner-shadow")[0];
    0 < e.length && t && e[0].addEventListener("ps-scroll-y", function() {
        this.querySelector(".ps__thumb-y").offsetTop ? t.style.display = "block" : t.style.display = "none"
    });
    var a, n = document.querySelector(".dropdown-style-switcher"), s = localStorage.getItem("templateCustomizer-" + templateName + "--Style") || (window.templateCustomizer?.settings?.defaultStyle ?? "light"), n = (window.templateCustomizer && n && ([].slice.call(n.children[1].querySelectorAll(".dropdown-item")).forEach(function(e) {
        e.addEventListener("click", function() {
            var e = this.getAttribute("data-theme");
            "light" === e ? window.templateCustomizer.setStyle("light") : "dark" === e ? window.templateCustomizer.setStyle("dark") : window.templateCustomizer.setStyle("system")
        })
    }),
    n = n.querySelector("i"),
    "light" === s ? (n.classList.add("ti-sun"),
    new bootstrap.Tooltip(n,{
        title: "Light Mode",
        fallbackPlacements: ["bottom"]
    })) : "dark" === s ? (n.classList.add("ti-moon"),
    new bootstrap.Tooltip(n,{
        title: "Dark Mode",
        fallbackPlacements: ["bottom"]
    })) : (n.classList.add("ti-device-desktop"),
    new bootstrap.Tooltip(n,{
        title: "System Mode",
        fallbackPlacements: ["bottom"]
    }))),
    "system" === (a = s) && (a = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"),
    [].slice.call(document.querySelectorAll("[data-app-" + a + "-img]")).map(function(e) {
        var t = e.getAttribute("data-app-" + a + "-img");
        e.src = assetsPath + "img/" + t
    }),
    document.getElementsByClassName("dropdown-language"));
    if (n.length) {
        var o = n[0].querySelectorAll(".dropdown-item");
        for (let e = 0; e < o.length; e++)
            o[e].addEventListener("click", function() {
                let a = this.getAttribute("data-language")
                  , n = this.getAttribute("data-text-direction");
                for (var e of this.parentNode.children)
                    for (var t = e.parentElement.parentNode.firstChild; t; )
                        1 === t.nodeType && t !== t.parentElement && t.querySelector(".dropdown-item").classList.remove("active"),
                        t = t.nextSibling;
                this.classList.add("active"),
                i18next.changeLanguage(a, (e,t)=>{
                    if (window.templateCustomizer && window.templateCustomizer.setLang(a),
                    "rtl" === n ? "true" !== localStorage.getItem("templateCustomizer-" + templateName + "--Rtl") && window.templateCustomizer && window.templateCustomizer.setRtl(!0) : "true" === localStorage.getItem("templateCustomizer-" + templateName + "--Rtl") && window.templateCustomizer && window.templateCustomizer.setRtl(!1),
                    e)
                        return console.log("something went wrong loading", e);
                    l()
                }
                )
            })
    }
    function l() {
        var e = document.querySelectorAll("[data-i18n]")
          , t = document.querySelector('.dropdown-item[data-language="' + i18next.language + '"]');
        t && t.click(),
        e.forEach(function(e) {
            e.innerHTML = i18next.t(e.dataset.i18n)
        })
    }
    s = document.querySelector(".dropdown-notifications-all");
    function i(e) {
        "show.bs.collapse" == e.type || "show.bs.collapse" == e.type ? e.target.closest(".accordion-item").classList.add("active") : e.target.closest(".accordion-item").classList.remove("active")
    }
    const r = document.querySelectorAll(".dropdown-notifications-read");
    s && s.addEventListener("click", e=>{
        r.forEach(e=>{
            e.closest(".dropdown-notifications-item").classList.add("marked-as-read")
        }
        )
    }
    ),
    r && r.forEach(t=>{
        t.addEventListener("click", e=>{
            t.closest(".dropdown-notifications-item").classList.toggle("marked-as-read")
        }
        )
    }
    ),
    document.querySelectorAll(".dropdown-notifications-archive").forEach(t=>{
        t.addEventListener("click", e=>{
            t.closest(".dropdown-notifications-item").remove()
        }
        )
    }
    ),
    [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]')).map(function(e) {
        return new bootstrap.Tooltip(e)
    });
    [].slice.call(document.querySelectorAll(".accordion")).map(function(e) {
        e.addEventListener("show.bs.collapse", i),
        e.addEventListener("hide.bs.collapse", i)
    });
    window.Helpers.setAutoUpdate(!0),
    window.Helpers.initPasswordToggle(),
    window.Helpers.initSpeechToText(),
    window.Helpers.initNavbarDropdownScrollbar();
    let d = document.querySelector("[data-template^='horizontal-menu']");
    if (d && (window.innerWidth < window.Helpers.LAYOUT_BREAKPOINT ? window.Helpers.setNavbarFixed("fixed") : window.Helpers.setNavbarFixed("")),
    window.addEventListener("resize", function(e) {
        window.innerWidth >= window.Helpers.LAYOUT_BREAKPOINT && document.querySelector(".search-input-wrapper") && (document.querySelector(".search-input-wrapper").classList.add("d-none"),
        document.querySelector(".search-input").value = ""),
        d && (window.innerWidth < window.Helpers.LAYOUT_BREAKPOINT ? window.Helpers.setNavbarFixed("fixed") : window.Helpers.setNavbarFixed(""),
        setTimeout(function() {
            window.innerWidth < window.Helpers.LAYOUT_BREAKPOINT ? document.getElementById("layout-menu") && document.getElementById("layout-menu").classList.contains("menu-horizontal") && menu.switchMenu("vertical") : document.getElementById("layout-menu") && document.getElementById("layout-menu").classList.contains("menu-vertical") && menu.switchMenu("horizontal")
        }, 100))
    }, !0),
    !isHorizontalLayout && !window.Helpers.isSmallScreen() && ("undefined" != typeof TemplateCustomizer && (window.templateCustomizer.settings.defaultMenuCollapsed ? window.Helpers.setCollapsed(!0, !1) : window.Helpers.setCollapsed(!1, !1)),
    "undefined" != typeof config) && config.enableMenuLocalStorage)
        try {
            null !== localStorage.getItem("templateCustomizer-" + templateName + "--LayoutCollapsed") && window.Helpers.setCollapsed("true" === localStorage.getItem("templateCustomizer-" + templateName + "--LayoutCollapsed"), !1)
        } catch (e) {}
}(),
"undefined" != typeof $ && $(function() {
    window.Helpers.initSidebarToggle();
    var t, a, e, n = $(".search-toggler"), s = $(".search-input-wrapper"), o = $(".search-input"), l = $(".content-backdrop");
    n.length && n.on("click", function() {
        s.length && (s.toggleClass("d-none"),
        o.focus())
    }),
    $(document).on("keydown", function(e) {
        var t = e.ctrlKey
          , e = 191 === e.which;
        t && e && s.length && (s.toggleClass("d-none"),
        o.focus())
    }),
    setTimeout(function() {
        var e = $(".twitter-typeahead");
        o.on("focus", function() {
            s.hasClass("container-xxl") ? (s.find(e).addClass("container-xxl"),
            e.removeClass("container-fluid")) : s.hasClass("container-fluid") && (s.find(e).addClass("container-fluid"),
            e.removeClass("container-xxl"))
        })
    }, 10),
    o.length && (t = function(n) {
        return function(t, e) {
            let a;
            a = [],
            n.filter(function(e) {
                if (e.name.toLowerCase().startsWith(t.toLowerCase()))
                    a.push(e);
                else {
                    if (e.name.toLowerCase().startsWith(t.toLowerCase()) || !e.name.toLowerCase().includes(t.toLowerCase()))
                        return [];
                    a.push(e),
                    a.sort(function(e, t) {
                        return t.name < e.name ? 1 : -1
                    })
                }
            }),
            e(a)
        }
    }
   ,
    o.on("keyup", function() {
        e.update()
    }))
});
