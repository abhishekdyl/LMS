"use strict";
    function _classCallCheck(t, e) {
        if (!(t instanceof e)) throw new TypeError("Cannot call a class as a function")
    }
    var _createClass = function() {
        function t(t, e) {
            for (var s = 0; s < e.length; s++) {
                var n = e[s];
                n.enumerable = n.enumerable || !1, n.configurable = !0, "value" in n && (n.writable = !0), Object.defineProperty(t, n.key, n)
            }
        }
        return function(e, s, n) {
            return s && t(e.prototype, s), n && t(e, n), e
        }
    }(),
    DashboardController = function() {
        function t() {
            _classCallCheck(this, t)
        }
        return _createClass(t, [{
            key: "init",
            value: function(t, e) {
                this.jQuery = $ = t,
                this.$form = e,
                this.baseURL = `${M.cfg.wwwroot}/blocks/dash/classes/local/widget/dashboard/dashboardapi.php`,
                this.activetab = null,
                // this.$maincontent = e.find("#profile-content-my-courses"),
                this.$maincontent = e.find("[block_dash_maincontent]"),
                this._initListeners()
            }
/*_initListeners*/
        }, {
            key: "_initListeners",
            value: function() {
                var that = this;
                this.activetab = this.$maincontent.data("activetab");
                if(!this.activetab){
                	this.$maincontent.attr("data-activetab", "mycourse");
                	this.activetab = "mycourse";
                }
                this.$form.on("click", "[tablink]", function(e) {that.tabchanged(this, e)}),

                
            	this.loadmaincontent();
            }
/*tabchanged*/
        }, {
            key: "tabchanged",
            value: function(element, event) {
                var activetab = this.jQuery(element).data("tab");
                if(activetab){
                	this.activetab=activetab;
                	this.loadmaincontent();
                }

            }
/*loadmaincontent*/
        }, {
            key: "loadmaincontent",
            value: function() {
            	console.log("aaaaaa1- ", this.activetab);
            	if(this.activetab){
            		this.$form.find("[tablink]").closest("li").removeClass("active");
            		this.$form.find(`[data-tab="${this.activetab}"]`).addClass("active");
	            	this.$maincontent.html(""),
	            	this.loadContent();
            	}
            }
/*loadmaincontent*/
        }, {
            key: "loadContent",
            value: function() {
            	console.log("aaaaaa2- ", this.activetab);
	            var that = this;
	            this._APICall(
	                this._prepareRequest(
	                    "getContent",
	                    {
	                        activetab:this.activetab
	                    }
	                ),
	                function (result) {
	                	console.log("result- ", result);
	                    console.log("this.activetab: ", that.activetab);
	                    that.$maincontent.prepend(result.data);
                        // that.$maincontent.html(`loading: ${that.activetab} with `+JSON.stringify(result));
	                }
	            );

            }
/*_prepareRequest*/
        }, {
            key: "_prepareRequest",
            value: function(wsfunction, data) {
                if(this.applang){
                    data.lang = this.applang;
                }
                var returndata = {
                    "wsfunction":wsfunction,
                    "wsargs":data
                }
                if(this.logintoken){
                    returndata.wstoken = this.logintoken;
                }
                return JSON.stringify(returndata);
            }
/*_APICall*/
        }, {
            key: "_APICall",
            value: function(requestdata, success) {
                var that = this;
                // console.log("requestdata- ", requestdata)
                // if(this.jCall){
                //     this.jCall.abort();
                // }
                this.jCall = $.ajax({
                    "url": this.baseURL,
                    "method": "POST",
                    "timeout": 0,
                    "headers": {
                        "Content-Type": "application/json"
                    },
                    "data": requestdata,
                    beforeSend:function (){
                        // console.log("request beforeSend");
                    },
                    success: function (data, textStatus, jqXHR) {
                        // console.log("data- ", data)
                        // if(data.code == 100){
                        //     // that.$apiLoader.removeClass("active");
                        //     // localStorage.setItem('logintoken', null);
                        //     // that._showLogin();
                        //     // displayToast(data.error.title, data.error.message, "error");
                        // } else if(data.code != 200){
                        //     // that.$apiLoader.removeClass("active");
                        //     // displayToast(data.error.title, data.error.message, "error");
                        // } else {
                            // that.premiumAccount = data.premiumAccount;
                            // that.premiumAccountExpiry = data.premiumAccountExpiry;
                            // that.remainingDays = data.remainingDays;
                            success(data);
                        // }
                    },error: function(){
                        // console.log("request error");
                        return null;
                    },complete: function(){
                        // console.log("request complete");
                    }
                });
            }
        }]), t
}();
!function(t) {
    t(function() {
        t("#block_dash_dashboard").each(function() {
            (new DashboardController).init(t, t(this))
        }), window.errors && window.errors.length && e.showMessage("Please correct the following errors:", window.errors)
    })
}(jQuery);