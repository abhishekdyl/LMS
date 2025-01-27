"use strict";
var dragSrcEl = null;
var touchEl = null;
var lastMove = null;
function dragStart(e) {
  dragSrcEl = this.cloneNode(false);
};

function dragEnter(e) {
  this.classList.add('drag-over');
}

function dragLeave(e) {
  e.stopPropagation();
  this.classList.remove('drag-over');
}

function dragOver(e) {
  e.preventDefault();
  return false;
}

function dragDrop(e) {
    $(this).html("");
  if (dragSrcEl.classList.contains('drag-item--prepend')) {
    this.prepend(dragSrcEl);
    // this.innerHTML(dragSrcEl);
  } else {
    dragSrcEl.innerHTML = ((dragSrcEl.dataset.type==1)?`<img src="${dragSrcEl.dataset.text}" />`:dragSrcEl.dataset.text); 

    this.appendChild(dragSrcEl);
    // this.innerHTML(dragSrcEl);
  }
  return false;
}

function dragEnd(e) {
  var listItems = document.querySelectorAll('.drag-container');
  [].forEach.call(listItems, function(item) {
    item.classList.remove('drag-over');
  });
}

function touchStart(e) {
  e.preventDefault();
  this.classList.add('drag-item--touchmove');
}

var scrollDelay = 0;
var scrollDirection = 1;
function pageScroll(a, b) {
  window.scrollBy(0,scrollDirection); // horizontal and vertical scroll increments
  scrollDelay = setTimeout(pageScroll,5); // scrolls every 100 milliseconds

  if (a > window.innerHeight - b) { scrollDirection = 1; }
  if (a < 0 + b) { scrollDirection = -1*scrollDirection; }
}

var x = 1;
function touchMove(e) {
  var touchLocation = e.targetTouches[0],
      w = this.offsetWidth,
      h = this.offsetHeight;

  lastMove = e;
  touchEl = this.cloneNode(false);
  this.style.width = w + 'px';
  this.style.height = h + 'px';
  this.style.position = 'fixed';
  this.style.left = touchLocation.clientX - w/2 + 'px';
  this.style.top = touchLocation.clientY - h/2 + 'px';

  if (touchLocation.clientY > window.innerHeight - h || touchLocation.clientY < 0 + h) {
    if (x === 1) {
      x = 0;
      pageScroll(touchLocation.clientY, h);
    }
  } else {
    clearTimeout(scrollDelay);
    x = 1;
  }
}

function touchEnd(e) {
  var box1 = this.getBoundingClientRect(),
      x1 = box1.left,
      y1 = box1.top,
      h1 = this.offsetHeight,
      w1 = this.offsetWidth,
      b1 = y1 + h1,
      r1 = x1 + w1;

  var targets = document.querySelectorAll('.drag-container');
  [].forEach.call(targets, function(target) {
    var box2 = target.getBoundingClientRect(),
        x2 = box2.left,
        y2 = box2.top,
        h2 = target.offsetHeight,
        w2 = target.offsetWidth,
        b2 = y2 + h2,
        r2 = x2 + w2;

    if (b1 < y2 || y1 > b2 || r1 < x2 || x1 > r2) {
      return false;
    } else {
        console.log("touchEl- ", touchEl)
        touchEl.style.position = "relative";
        touchEl.style.left = "unset";
        touchEl.style.top = "unset";
        touchEl.innerHTML = ((touchEl.dataset.type==1)?`<img src="${touchEl.dataset.text}" />`:touchEl.dataset.text); 
        // console.log("target- ", $(target).html("fdsfsdf"))
        $(target).html("");
      if (touchEl.classList.contains('drag-item--prepend')) {
        target.prepend(touchEl);
        // target.html(touchEl);
      } else {
        target.appendChild(touchEl);
        // target.html(touchEl);
      }
    }
  });

  this.removeAttribute('style');
  this.classList.remove('drag-item--touchmove');
  clearTimeout(scrollDelay);
  x = 1;
}


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
    AppController = function() {
        function t() {
            _classCallCheck(this, t)
        }
        return _createClass(t, [{
            key: "init",
            value: function(t, e) {
                this.jQuery = $ = t,
                this.$form = e,
                this.$form_Login = e.find("#loginContainer"),
                this.$form_Language = e.find("#langContainer"),
                this.$form_SubjectView = e.find("#subjectviewContainer"),
                this.$form_WorldView = e.find("#worldviewContainer"),
                this.$wordviewsidebar = e.find("[wordviewsidebar]"),
                this.$form_regionView = e.find("#regionviewContainer"),
                this.$form_subtopicView = e.find("#subtopicviewContainer"),
                this.$form_videoOverview = e.find("#videoOverviewContainer"),
                this.$btn_loadworkdview = e.find("[loadworkdview]"),
                this.$btn_loadleaderboard = e.find("[loadleaderboard]"),
                this.$btn_loadprofile = e.find("[loadprofile]"),
                this.$OverviewVideoPlayer = e.find("[OverviewVideoPlayer]"),
                this.$OverviewPlayer = e.find("[OverviewPlayer]"),
                this.$OverviewPlayerbtn = e.find("[OverviewPlayerbtn]"),
                this.$exammode_enabled = e.find("[exammode_enabled]"),
                this.$apiLoader = e.find("#apiLoader"),
                // this.baseURL = "api/index.php",
                this.baseURL = "https://portal1.fivestudents.com/api/v1",
                this.logintoken = null,
                this.applang = null,
                this.getMainAccount = null,
                this.user = null,
                this.currentUser = null,
                this.coursedata = null,
                this.gradedata = null,
                this.examMode = false,
                this.loadedexamMode = false,
                this.loadedcourse = null,
                this.loadedtopic = null,
                this.loadedtopics = [],
                this.loadedsubtopic = null,
                this.loadedquiz = null,
                this.loadedquiz_index = null,
                this.gradeview = "grade",
                this.monthlyreport = "grade",
                this.leaderboard_timeframe = "all",
                this.leaderboard_boundry = "national",
                this.backtocourseview = null,
                this.$myrankdetails = e.find("[myrankdetails]"),
                this.$myrankdetails_rank = e.find("[myrankdetails] [rank]"),
                this.$myrankdetails_profile = e.find("[myrankdetails] [profile]"),
                this.$myrankdetails_name = e.find("[myrankdetails] [name]"),
                this.$myrankdetails_score = e.find("[myrankdetails] [score]"),
                this.$src_currentchiltimage = e.find("[userprofileimage]"),
                this.$txt_firstname = e.find("[txt_firstname]"),
                this.$txt_lastname = e.find("[txt_lastname]"),
                this.$txt_charname = e.find("[txt_charname]"),
                this.$select_grades = e.find("[select_grades]"),
                this.$subtopicsecondaryimage = e.find("[subtopicsecondaryimage]"),
                this.$subtopcresultsection = e.find("[subtopcresultsection]"),
                this.$startquiz = e.find("[startquiz]"),
                this.$restartquiz = e.find("[restartquiz]"),
                this.$startnextquiz = e.find("[startnextquiz]"),
                this.$quizlistsectionmessage1 = e.find("[quizlistsectionmessage1]"),
                this.$quizlistsectionmessage2 = e.find("[quizlistsectionmessage2]"),
                this.$qplayer = e.find("[qplayer]"),
                this.$qplayer_header = e.find("[qplayer] [qplayerheader]"),
                this.$qplayer_question_title = e.find("[qplayer] [questiontitle]"),
                
                this.$qplayer_qplayertimercounter = e.find("[qplayer] [qplayertimercounter]"),
                this.$qplayer_qplayertimer = e.find("[qplayer] [qplayertimer]"),
                this.$qplayer_qplayertimerprogress = e.find("[qplayer] [qplayertimerprogress]"),
                this.$qplayer_timer = null,
                this.$plusplayer_timer = null,

                this.$qplayer_close = e.find("[qplayer] [qplayerclose]"),
                this.$qplayer_pagination = e.find("[qplayer] [qpagination]"),
                this.$qplayer_close_popup = e.find("[qplayer] [qplayerclosepopup]"),
                this.$qplayer_question_text = e.find("[qplayer] [questiontext]"),
                this.$qplayer_question_submit = e.find("[qplayer] [questionsubmit]"),
                this.$qplayer_question_toggle = e.find("[qplayer] [questiontoggle]"),
                this.$qplayer_question_help = e.find("[qplayer] [questionhelp]"),
                this.$qplayer_question_toggletext = "",
                this.$qplayer_questiontoggle = e.find("[qplayer] [qplayertoggle]"),
                this.$qplayer_questiontoggle_text = e.find("[qplayer] [qplayertoggle] [qplayertoggletext]"),
                this.$qplayerrbtnlist = e.find("[qplayerrbtnlist]"),
                this.$qplayer_question_prev = e.find("[qplayer] [prevquestion]"),
                this.$qplayer_question_next = e.find("[qplayer] [nextquestion]"),
                this.$qplayer_question_translation = e.find("[qplayer] [questiontranslation]"),
                this.$qplayer_question_hints = e.find("[qplayer] [questionhints]"),
                this.$qplayer_question_correction = e.find("[qplayer] [questioncorrection]"),
                this.$qplayer_question_current = 0,
                this.$qplayer_question_total = 0,
                this.$qplayer_finished = 0,
                this.$qplayer_pagination_current = e.find("[qplayer] [paginationcurrent]"),
                this.$qplayer_pagination_total = e.find("[qplayer] [paginationtotal]"),
                this.$languageselector = e.find("[languageselector]"),
                this.$changelanguagemodal = e.find("[changelanguagemodal]"),
                this.$confirmationpopup = e.find("[confirmationpopup]"),
                this.$confirmationpopuptitle = e.find("[confirmationpopuptitle]"),
                this.$confirmationpopupmsg1 = e.find("[confirmationpopupmsg1]"),
                this.$confirmationpopupmsg1_1 = e.find("[confirmationpopupmsg1_1]"),
                this.$confirmationpopupmsg2 = e.find("[confirmationpopupmsg2]"),
                this.$confirmationpopupclose = e.find("[confirmationpopupclose]"),
                this.$messagecenterContainer = e.find("[messagecenterContainer]"),
                this.$reportcenterContainer = e.find("[reportcenterContainer]"),
                this.$reportviewContainer = e.find("[reportviewContainer]"),
                this.$monthlyreport = e.find("[monthlyreport]"),
                this.$reporttablebody = e.find("[reporttablebody]"),
                this.$quizplayerContainer = e.find("[quizplayerContainer]"),
                this.$apphomework_ongoing = e.find("[apphomework_ongoing]"),
                this.$apphomework_completed = e.find("[apphomework_completed]"),
                this.$apphomework_notcompleted = e.find("[apphomework_notcompleted]"),
                this.$apphomework_retry = e.find("[apphomework_retry]"),
                this.$appcontroles_ongoing = e.find("[appcontroles_ongoing]"),
                this.$appcontroles_completed = e.find("[appcontroles_completed]"),
                this.$appcontroles_notcompleted = e.find("[appcontroles_notcompleted]"),
                this.$forcefullscreen = e.find("[forcefullscreen]"),
                this.$reguinselectiondata = [],
                this.$qplayer_data = null,
                this.$qplayer_type = 0,
                this.$currentgrade = 0,
                this.$topicprevpage = e.find("[topicprevpage]"),
                this.$topicnextpage = e.find("[topicnextpage]"),
                this.$subtopicprevpage = e.find("[subtopicprevpage]"),
                this.$subtopicnextpage = e.find("[subtopicnextpage]"),
                this.premiumAccount = false;
                this.premiumAccountExpiry = 0;
                this.remainingDays = 0;
                this.wordviewtopic_page = 0;
                this.regionviewsubtopic_page = 0;
                this.btnlimit = 8;
                this.remainingDays = 0;
                this.$coursetitlebg = e.find("[coursetitlebg]"),
                this.$coursesubtitlebg = e.find("[coursesubtitlebg]"),
                this.$seconddiagnostic = e.find("[seconddiagnostic]"),
                this.jCall = null,
                this.audioplayer = null,
                this.audioplayerurl = null,
                this.onlycharupdate = false,
                this.currentframe = null,
                this.lastframe = null,
                this.loadLevelWorldViewargs = null,
                this._initListeners()
            }
/*Login*/
        }, {
            key: "_login",
            value: function() {
                var username_input = this.$form_Login.find("[name=\"username\"]"),
                password_input = this.$form_Login.find("[name=\"password\"]"),
                username = username_input.val(),
                password = password_input.val();
                var errormsg = [];
                if(!username){
                    errormsg.push("username is required"); 
                }
                if(!password){
                    errormsg.push("password is required"); 
                }
                if(errormsg.length > 0){
                    displayToast("Error", errormsg.join(", "), "error");
                } else {
                    var that = this;
                    that.$apiLoader.addClass("active");
                    this._APICall(
                        this._prepareRequest(
                            "login",
                            {
                                logintype:"normal",
                                username:username,
                                password:password,
                                devicetoken:"web",
                                devicename:"web",
                            }
                        ),
                        function (result) {
                            username_input.val(""),
                            password_input.val("");

                            if(result.data.token){
                                localStorage.setItem('logintoken', result.data.token);
                                localStorage.setItem('userdetails', JSON.stringify(result.data.userDetails));
                                that._checkLogin()
                            }
                            that.$apiLoader.removeClass("active");
                        }
                    );
                }
            }
/*_languageSelected*/
        }, {
            key: "_languageSelected",
            value: function() {
                var selectedlang = this.$form_Language.find("[name=\"applanguage\"]").val();
                var errormsg = [];
                if(!selectedlang){
                    errormsg.push("language is required is required"); 
                }
                if(errormsg.length > 0){
                    displayToast("Error", errormsg.join(", "), "error");
                } else {
                    localStorage.setItem('applang', selectedlang);
                    this._checkLogin();
                }
            }
/*_getcurrentlanguage*/
        }, {
            key: "_getcurrentlanguage",
            value: function() {
                this.applang = localStorage.getItem('applang');
                if(this.applang){
                    return this.applang;
                } else {
                    return "fr";
                }
            }
/*_getstring*/
        }, {
            key: "_getstring",
            value: function(key, data='') {
                var lang = this._getcurrentlanguage();
                var foundmessage = '';
                if(langdata[lang] && langdata[lang][key]){
                    foundmessage = langdata[lang][key];
                } else if(langdata['fr'][key]) {
                    foundmessage = langdata['fr'][key];
                }
                if(foundmessage){
                    if(typeof data == "string" || typeof data == "number"){
                        foundmessage = foundmessage.replace("{a}", data);
                    } else if(typeof data == "object"){
                        let allkeys = Object.keys(data);
                        for (const key of allkeys) {
                            foundmessage = foundmessage.replace(`{a->${key}}`, data[key]);
                        }
                    }
                    return foundmessage;
                } else {
                    return `[${key}]`;
                }
            }
/*_updateLanguageString*/
        }, {
            key: "_updateLanguageString",
            value: function() {
                var that = this;
                this.jQuery("[data-langstring]").each(function(index) {
                    var place = that.jQuery(this).data("langplace");
                    var languagekey = that.jQuery(this).data("langstring");
                    var languageval = that._getstring(languagekey);
                    switch(place) {
                      case 'placeholder':
                        that.jQuery(this).attr("placeholder", languageval);
                        break;
                      case "text":
                        that.jQuery(this).text(languageval);
                        break;
                      case "html":
                        that.jQuery(this).html(languageval);
                        break;
                      default:
                    }
                });
            }
/*_initListeners*/
        }, {
            key: "_initListeners",
            value: function() {
                var that = this;
                this.wordviewtopic_page = 0;
                this.regionviewsubtopic_page = 0;
                this.$form.on("click", "[submitlogin]", function(e) {that._login()}),
                this.$form.on("click", "[tooglesidebar]", function(e) {that.$wordviewsidebar.toggleClass("open");}),
                this.$form.on("click", "[savelanguage]", function(e) {that._languageSelected()}),
                this.$form.on("click", "[backtocourseview]", function(e) {
                    that.$apiLoader.addClass("active");
                    if(that.loadedtopic && that.loadedtopic?.parent != "0"){
                        that.loadedtopic = that.loadedtopics[that.loadedtopic.parent],
                        that._loadtopicView();
                    } else {
                        that._loadWorldViewData();
                        // that._showframe("worldviewContainer");
                    }
                    that.$apiLoader.removeClass("active");
                    console.log("backtocourseview clicked")
                }),
                this.$form.on("click", "[backtotopicview]", function(e) {
                    that.$apiLoader.addClass("active");
                    if(that.loadedtopic && Array.isArray(that.loadedtopic.subtopics) && that.loadedtopic.subtopics.length > 1){
                        that._loadtopicView();
                    } else {
                        that._loadalltopics();
                    }
                    that.$apiLoader.removeClass("active");
                    console.log("backtotopicview clicked")
                }),
                this.$form.on("click", "[loadworkdview]", function(e) {that._reloadapp()}),
                this.$form.on("click", "[loadexamview]", function(e) {that._reloadapp(true)}),
                this.$form.on("click", "[loadleaderboard]", function (e) {
                    that.leaderboard_timeframe = "all",
                    that.leaderboard_boundry = "national",
                    that._loadleaderboard();
                }),
                this.$form.on("click", "[loadleaderboard_timeframe]", function (e) {
                    that.leaderboard_timeframe = that.jQuery(this).data("value");
                    console.log("that.leaderboard_boundry ", that.leaderboard_boundry);
                    console.log("that.leaderboard_timeframe ", that.leaderboard_timeframe);
                    that._loadleaderboard();
                }),
                this.$form.on("click", "[loadleaderboard_bountry]", function (e) {
                    that.leaderboard_boundry = that.jQuery(this).data("value");
                    console.log("that.leaderboard_boundry ", that.leaderboard_boundry);
                    console.log("that.leaderboard_timeframe ", that.leaderboard_timeframe);
                    that._loadleaderboard();
                }),
                this.$form.on("click", "[loadprofile]", function(e) {that._loadprofile()}),
                this.$form.on("click", "[logout]", function(e) {that._logout()}),
                this.$form.on('click','[loadgeneraterefkey]',function(e){that._loadgeneraterefkey()}),
                this.$form.on("click", "[updateProfile]", function(e) {that._updateProfile()}),
                this.$form.on("click", "[changequiz]", function(e) {that._changequiz(this, e)}),
                this.$form.on("click", "[startquiz]", function(e) {that._startquiz(this, e)}),
                this.$form.on("click", "[restartquiz]", function(e) {that._startquiz(this, e)}),
                this.$form.on("click", "[startnextquiz]", function(e) {that._startnextquiz(this, e)}),
                this.$form.on("click", ".active[nextquestion]", function(e) {that._nextquestion(1)}),
                this.$form.on("click", ".active[prevquestion]", function(e) {that._nextquestion(0)}),
                this.$form.on("click", "[questionsubmit]", function(e) {that._questionsubmit()}),
                this.$form.on("click", "[questiontoggle]", function(e) {that._questiontoggle()}),
                this.$form.on("click", "[questionhelp]", function(e) {that._questionhelp(this, e)}),
                this.$form.on("click", "[qplayertoggleremove]", function(e) {that._qplayertoggleremove()}),
                this.$form.on("click", "[qplayerclose]", function(e) {that._qplayerclose()}),
                this.$form.on("click", "[qplayercloseconfirm]", function(e) {that._qplayercloseconfirm()}),
                this.$form.on("click", "[qplayercloseskip]", function(e) {that._qplayercloseskip()}),
                this.$form.on("click", "[changelanguage]", function(e) {that._changelanguage()}),
                this.$form.on("click", "[openchangelanguage]", function(e) {that._togglechangelanguage()}),
                this.$form.on("click", "[closechangelanguage]", function(e) {that._togglechangelanguage()}),
                this.$form.on("click", "[messagecenter]", function(e) {that._messagecenter('ongoing')}),
                this.$form.on("click", "[reportcenter]", function(e) {that._reportcenter('ongoing')}),
                this.$form.on('click','[questiontranslation]',function(e){that._questiontranslation(this)}),
                this.$form.on('click','[questionhints]',function(e){that._questionhints(this)}),
                this.$form.on('click','[questioncorrection]',function(e){that._questioncorrection(this)}),
                this.$form.on('click','[closemessagecenter]',function(e){that._closemessagecenter()}),
                this.$form.on('click','[closereportcenter]',function(e){that._closereportcenter()}),
                this.$form.on('click','[closereportview]',function(e){that._closereportview()}),
                this.$form.on('click','[viewmonthlyreport]',function(e){that._viewmonthlyreport(this, e)}),
                this.$form.on('click','[starthmquiz]',function(e){that._starthmquiz(this)}),
                this.$form.on('click','[gotofullscreen]',function(e){that._gotofullscreen(this, e)}),
                this.$form.on('click','[characterthumb]',function(e){that._characterthumb(this, e)}),
                this.$form.on('click','[selectcharacter]',function(e){that._selectcharacter()}),
                this.$form.on('click','[forcefullscreen]',function(e){that._forcefullscreen()}),
                this.$form.on('click','[updatemyregion]',function(e){that._updatemyregion()}),
                this.$form.on('change','[regionselector]',function(e){that._updateprovince()}),
                this.$form.on('click','[backtosubject]',function(e){that._backtosubject()}),
                this.$form.on('click','[topicnextpage]',function(e){that._topicnextpage()}),
                this.$form.on('click','[subtopicprevpage]',function(e){that._subtopicprevpage()}),
                this.$form.on('click','[subtopicnextpage]',function(e){that._subtopicnextpage()}),
                this.$form.on('click','[toggleplay]',function(e){that._toggleplay(this)}),
                this.$form.on('click','[confirmationpopupclose]',function(e){
                    var action = that.$confirmationpopup.data("action");
                    if(action == "playOverviewVideo"){
                        console.log("this.loadedcourse- ", that.loadedcourse);
                        let questionVideo = that.loadedcourse?.overviewVideo,
                        questionVideoTitle =that.loadedcourse?.shortName;
                        const videoplayer = `<div class='fullScreenPlayer'><div class="plusplayer"><span class="customvideo" diagnosticVideo src="${questionVideo}" data-title="${questionVideoTitle}" id="${that.loadedcourse.id}"></span></div></div>`;
                        // const videoplayer = `<div class="CVPlayer"><div class="CVPlayerRatio"></div><span class="customvideo" src="${questionVideo}" data-title="${questionVideoTitle}"></span></div>`;
                        that.$OverviewPlayer.html(videoplayer);
                        $('[diagnosticVideo]').stylise(that.loadedcourse.id);
                        that.$OverviewPlayerbtn.removeClass("active");
                        sessionStorage.setItem(`stylised-time-progress-generated-video-player-${that.loadedcourse.id}`, 0);

                        that._showframe("videoOverviewContainer");
                        that.$OverviewPlayerbtn.data("id", that.loadedcourse?.diagnosticQuiz);
                        that.$plusplayer_timer = setInterval(() => { that._checkDiagnosticVideo(that.loadedcourse.id); }, 1000);
                    } else if(action == "startDiagnosticQuiz"){
                        const id = that.$confirmationpopup.data("diagid");
                        if(id){
                            that.$plusplayer_timer = null;
                            that.$OverviewPlayer.html("");
                            clearInterval(that.$plusplayer_timer);                        
                            that._starttarlquizplayer(id);
                        }
                    } else if(action == "loadLevelWorldView"){
                        that._loadLevelWorldView(that.loadLevelWorldViewargs);
                    } else {

                    }
                    that.$confirmationpopup.data("action", "");
                    that.$confirmationpopup.data("diagid", "");
                    that.$confirmationpopup.hide();
                }),
                this.$form.on('click','[OverviewPlayerbtn]',function(e){
                    const id = that.jQuery(this).data("id");
                    if(id){
                        clearInterval(that.$plusplayer_timer);
                        that.$confirmationpopuptitle.html(that._getstring(`language_afterdiagvideotitle`));
                        that.$confirmationpopupmsg1.html(that._getstring(`language_afterdiagvideoline1`));
                        that.$confirmationpopupmsg2.html("");
                        that.$confirmationpopup.data("action", "startDiagnosticQuiz");
                        that.$confirmationpopup.data("diagid", id);
                        that.$confirmationpopup.show();
                        that._viewDiagnostic();
                    }
                }),
                this.$form.on('click','.qtext .selectable',function(e){that._toggleselectable(this)}),
                this.$form.on('click','[testbtn]', function(e){
                    var point= 1000;
                    var pointlevel = that.jQuery(this).data("label");
                    console.log(`pointlevel: ${pointlevel}, point: ${point}`);
                    that.$confirmationpopuptitle.html(that._getstring(`language_${pointlevel}title`, "  المستوى 2  "));
                    that.$confirmationpopupmsg1.html(that._getstring(`language_${pointlevel}1`, " المستوى 2 "));
                    that.$confirmationpopupmsg1_1.html(that._getstring(`language_${pointlevel}xp`, point));
                    that.$confirmationpopupmsg2.html(that._getstring(`language_${pointlevel}2`, ""));
                    that.$confirmationpopup.show();

                }),
                this.$form.on('click','[popclose]',function(e){
                    console.log('popclose');
                    that.jQuery(this).closest('[playerpop]').removeClass('active');
                    that.jQuery('[pophead]').html('');
                    that.jQuery('[poptop]').html('');
                    that.jQuery('[popbottom]').html('');
                }),
                this.$form.on('click','[appbtncourse]',function(e){that._appbtncourseclicked(this)}),
                this.$form.on('click','[appbtntopic]',function(e){that._appbtntopicclicked(this)}),
                this.$form.on('click','[appbtnsubtopic]',function(e){that._appbtnsubtopicclicked(this)}),
                this.$form.on('click','[userprofileimage]',function(e){that._startcharacterupdate()}),
                this.$form.on('click','[seconddiagnostic]',function(e){
                    var id = that.jQuery(this).data("id");
                    console.log("id- ", id);
                    that._starttarlquizplayer(id);
                }),
                this._updateLanguageString(),
                // this.jQuery("[forcefullscreen]").trigger("click");
                this._checkLogin(),
                console.log("_initListeners");
            }
/*stopAudioPlaying*/
        }, {
            key: "stopAudioPlaying",
            value: function() {
                if(this.audioplayer){
                    this.audioplayer.pause();
                    this.jQuery("[toggleplay]").removeClass("active");
                    this.jQuery("[toggleplay]").toggleClass("fa-play-circle fa-pause-circle");
                }
            }
/*_toggleselectable*/
        }, {
            key: "_toggleselectable",
            value: function(element) {
                var that=this;
                var eleid=this.jQuery(element).attr("for");
                if(this.jQuery(element).hasClass("selected")){
                    this.jQuery(`#${eleid}`).prop('checked', false);
                    this.jQuery(`#${eleid}`).removeAttr("value");
                    this.jQuery(element).removeClass("selected");
                } else {
                    this.jQuery(`#${eleid}`).prop('checked', true);
                    this.jQuery(`#${eleid}`).val("on");
                    this.jQuery(element).addClass("selected");
                }
            }
/*_toggleplay*/
        }, {
            key: "_toggleplay",
            value: function(element) {
                var that=this;
                var dataurl=this.jQuery(element).data("url");
                if(dataurl != this.audioplayerurl){
                    this.stopAudioPlaying();
                    this.audioplayer = new Audio(dataurl);
                    this.audioplayerurl = dataurl;
                }
                if (this.audioplayer.paused || this.audioplayer.ended) {
                    this.audioplayer.play();
                    this.jQuery(element).addClass("active");
                    this.jQuery(element).toggleClass("fa-play-circle fa-pause-circle");
                } else {
                    this.audioplayer.pause();
                    this.jQuery(element).toggleClass("fa-play-circle fa-pause-circle");
                    this.jQuery(element).removeClass("active");
                }
            }
/*_loadgeneraterefkey*/
        }, {
            key: "_loadgeneraterefkey",
            value: function() {
                console.log("loadgeneraterefkey clicked"),
                this._showframe("generaterefkeyContainer");
            }
/*_topicprevpage*/
        }, {
            key: "_topicprevpage",
            value: function() {
                if(this.wordviewtopic_page > 0){
                    this.wordviewtopic_page --;
                    this._loadWorldViewData();
                }
            }
/*_topicnextpage*/
        }, {
            key: "_topicnextpage",
            value: function() {
                if(this.wordviewtopic_page * this.btnlimit < this.loadedcourse.topics.length){
                    this.wordviewtopic_page ++;
                    this._loadWorldViewData();
                }
            }
/*_backtosubject*/
        }, {
            key: "_backtosubject",
            value: function() {
                if(Array.isArray(this.coursedata.courses) && this.coursedata.courses.length > 1){
                    this._loadSubjectViewData();
                }
            }
/*_subtopicprevpage*/
        }, {
            key: "_subtopicprevpage",
            value: function() {
                if(this.regionviewsubtopic_page > 0){
                    this.regionviewsubtopic_page --;
                    this._loadtopicView();
                }
            }
/*_subtopicnextpage*/
        }, {
            key: "_subtopicnextpage",
            value: function() {
                if(this.regionviewsubtopic_page * this.btnlimit < this.loadedtopic.subtopics.length){
                    this.regionviewsubtopic_page ++;
                    this._loadtopicView();
                }
            }
/*_forcefullscreen*/
        }, {
            key: "_forcefullscreen",
            value: function() {
                alert("_forcefullscreen")
            }
/*_updatemyregion*/
        }, {
            key: "_updatemyregion",
            value: function() {

                var region = this.jQuery("[regionselector]").val();
                var provinces = this.jQuery("[provinceselector]").val();
                if(!region){
                    displayToast("Error", "Please select region", 'error');
                } else if(!provinces){
                    displayToast("Error", "Please select provience", 'error');
                } else {
                    var that = this;
                    this._APICall(
                        this._prepareRequest(
                            "setRegions",
                            {
                                region:region,
                                provinces:provinces,
                                childid:that.getMainAccount.currentChild.id,
                                lang:this.applang
                            }
                        ),
                        function (result) {
                            that._reloadapp()
                        }
                    );                  
                }
            }
/*_startcharacterupdate*/
        }, {
            key: "_startcharacterupdate",
            value: function() {
                this.onlycharupdate = true,
                this._loadcharacterselectionView();
            }
/*_selectcharacter*/
        }, {
            key: "_selectcharacter",
            value: function() {

                var imagepath = this.jQuery("[selectedcharacter]").val();
                if(imagepath){
                    var that = this;
                    this._APICall(
                        this._prepareRequest(
                            "setCharacter",
                            {
                                image:imagepath,
                                childid:that.getMainAccount.currentChild.id,
                                lang:this.applang
                            }
                        ),
                        function (result) {
                            that.getMainAccount.currentChild.charImage=imagepath;
                            if(that.onlycharupdate){
                                that._updateUserAppearance();
                                that._showframe(that.lastframe);
                            } else {
                                that._reloadapp()
                            }
                        }
                    );                  
                } else {
                    displayToast("Error", "Please select Character", 'error');
                }
            }
/*_characterthumb*/
        }, {
            key: "_characterthumb",
            value: function(element, event) {
                this.jQuery("[characterthumb]").removeClass("active"),
                this.jQuery(element).addClass("active"),
                this.jQuery("[selectedcharacter]").val(this.jQuery(element).find("img").attr("src"));

            }
/*_gotofullscreen*/
        }, {
            key: "_gotofullscreen",
            value: function(element, event) {
          const elem = document.documentElement;
          console.log("elem.requestFullscreen- ", elem.requestFullscreen);
          try {
            if (elem.requestFullscreen) {
                elem.requestFullscreen()
            } else if (elem.webkitRequestFullScreen ) {
                elem.webkitRequestFullScreen()
            } else if (elem.mozRequestFullScreen ) {
                elem.mozRequestFullScreen()
            } else if (elem.msRequestFullscreen) {
                elem.msRequestFullscreen()
            } else {
              alert("---- fullscreen not available");
            }
            // $('#overlay-conatiner').fadeOut(2000);
          } catch(err) {
            alert(err.message);
          }            }
/*_starthmquiz*/
        }, {
            key: "_starthmquiz",
            value: function(element, event) {
                var cmid = this.jQuery(element).data("quiz");
                var position = this.jQuery(element).data("position");
                console.log("cmid", cmid);
                if(cmid){
                    if(position){
                        this.$qplayerrbtnlist.hide();
                    } else {
                        this.$qplayerrbtnlist.show();
                    }
                    this.$apiLoader.addClass("active"),
                    this.$qplayer_close_popup.removeClass("active"),
                    this.$qplayer_type = 1,
                    this.$qplayer_finished = 0,
                    this._startquizplayer(cmid),
                    console.log("Starting homework", cmid);
                } else {
                    displayToast("Falied", "quiz", "error");
                }
            }
/*_generatehmbutton*/
        }, {
            key: "_generatehmbutton",
            value: function(quiz, index, position, type) {
                console.log("quiz- ", quiz);
                var that = this,
                rightside = `<button starthmquiz data-quiz="${quiz.quizId}" data-position="${position}" class="starthomework">${that._getstring("language_messagecenter_startnow")}<img src="/assets/images/ic_arrow2.PNG" class="starthmquiz-in"/></button>`,
                quizhtml = ``;
                if(position && type == "notCompleted"){rightside =``;}
                if(quiz.completedDate){
                    rightside = `<div class="hmbox-body-right-item">
                                <span class="hmbox-body-right-label">${that._getstring("language_eventscreen_note")} <i class="fa-solid fa-star"></i> </span>
                                <span class="hmbox-body-right-data">${(quiz.bestGrade?quiz.bestGrade:'-')}/${quiz.maxScore}</span>
                              </div>
                            <div class="hmbox-body-right-item">
                                <span class="hmbox-body-right-label">XP <img style="width: 20px;" src="/assets/images/qpoint-xp.png"> </span>
                                <span class="hmbox-body-right-data">${(quiz.xp?quiz.xp:'-')}</span>
                              </div>
                              <div class="hmbox-body-right-item">
                                <span class="hmbox-body-right-label">Date of achhivement</span>
                                <span class="hmbox-body-right-data"><br/>${that._dateformat(quiz.completedDate)}</span>
                              </div>
                              <div class="hmbox-body-right-item">
                                ${((type == 'retry')?`<button starthmquiz data-quiz="${quiz.quizId}" data-position="${position}" class="starthomework">${that._getstring("language_messagecenter_startnow")}<img src="/assets/images/ic_arrow2.PNG" class="starthmquiz-in"/></button>`:``)}
                              </div>`;
                }
                quizhtml = `<div class="hmbox">
                          <div class="hmbox-head"><i class="fa-solid fa-stopwatch"></i> ${that._getstring("language_messagecenter_duedate")}:${that._dateformat(quiz.duedate)}</div>
                          <div class="hmbox-body">
                            <div class="hmbox-body-left">
                              <div class="hmbox-body-left-item">
                                <span class="hmbox-body-left-label"><span><i class="fa-solid fa-hyphen"></i></span> ${that._getstring("language_messagecenter_semester")}:</span>
                                <span class="hmbox-body-left-data">${quiz.semester}</span>
                              </div>
                              <div class="hmbox-body-left-item">
                                <span class="hmbox-body-left-label"><span><i class="fa-solid fa-hyphen"></i></span> ${that._getstring("language_messagecenter_lesson")}:</span>
                                <span class="hmbox-body-left-data">${quiz.lesson}</span>
                              </div>
                              <div class="hmbox-body-left-item">
                                <span class="hmbox-body-left-label"><span><i class="fa-solid fa-hyphen"></i></span> ${that._getstring("language_messagecenter_quizname")}:</span>
                                <span class="hmbox-body-left-data">${quiz.quizname}</span>
                              </div>
                            </div>
                            <div class="hmbox-body-right">${rightside}</div>
                          </div>
                        </div>`;
                return quizhtml;

            }
/*_loadhmquiz*/
        }, {
            key: "_loadhmquiz",
            value: function(allquiz, position, type) {
                var that = this,
                quizhtml = ``;
                if(allquiz && allquiz.length > 0){
                    allquiz.forEach(function(element, index) {
                        quizhtml+= that._generatehmbutton(element, index, position, type);
                    });
                } else {
                    quizhtml+= `
                    <div class="nohmquiz">${(position?that._getstring("language_messagecenter_noassignment"):that._getstring("language_messagecenter_nohomework"))}</div>
                    `;

                }
                return quizhtml;
            }
/*_processallitems*/
        }, {
            key: "_processallitems",
            value: function(data, position) {
                console.log("data - ", data)
                console.log("data - ", position)
                switch (position) {
                    case 0:
                        if(data.ongoing){
                            this.$apphomework_ongoing.html(this._loadhmquiz(data.ongoing, position, 'ongoing')); 
                        }
                        if(data.completed){
                            this.$apphomework_completed.html(this._loadhmquiz(data.completed, position, 'completed')); 
                        }
                        if(data.notCompleted){
                            this.$apphomework_notcompleted.html(this._loadhmquiz(data.notCompleted, position, 'notCompleted')); 
                        }
                        if(data.retry){
                            this.$apphomework_retry.html(this._loadhmquiz(data.retry, position, 'retry')); 
                        }
                        break;
                    case 1:
                        if(data.ongoing){
                            this.$appcontroles_ongoing.html(this._loadhmquiz(data.ongoing, position, 'ongoing')); 
                        }
                        if(data.completed){
                            this.$appcontroles_completed.html(this._loadhmquiz(data.completed, position, 'completed')); 
                        }
                        if(data.notCompleted){
                            this.$appcontroles_notcompleted.html(this._loadhmquiz(data.notCompleted, position, 'notCompleted')); 
                        }
                        break;
                }

            }
/*_dateformat*/
        }, {
            key: "_dateformat",
            value: function(timestamp, format="DD-MM-YYYY h:mm") {
                return moment(parseInt(timestamp)*1000).format(format);
            }
/*_closemessagecenter*/
        }, {
            key: "_closemessagecenter",
            value: function() {
                this.$messagecenterContainer.hide(),
                this.$apiLoader.removeClass("active"),
                console.log("message center closed");
            }
/*_messagecenter*/
        }, {
            key: "_messagecenter",
            value: function(view="ongoing") {
                var that = this;
                that.$apiLoader.addClass("active"),
                that._APICall(
                    that._prepareRequest(
                        "getMyExam",
                        {
                            lang:that.applang
                        }
                    ),
                    function (result) {
                        that.$apiLoader.addClass("active"),
                        that._processallitems(result.data.exam, 1),
                        that._APICall(
                            that._prepareRequest(
                                "getMyHomework",
                                {
                                    lang:that.applang
                                }
                            ),
                            function (result) {
                                console.log("result- ", result);
                                that.$apiLoader.removeClass("active"),
                                that._processallitems(result.data.homework, 0),
                                that.jQuery(`#apphomework a.nav-link, #appcontroles a.nav-link, #apphomework .tab-pane, #apphomework .tab-pane `).removeClass("active"),
                                that.jQuery(`#apphomework a.nav-link, #appcontroles a.nav-link, #apphomework .tab-pane, #apphomework .tab-pane `).removeClass("show"),
                                that.$messagecenterContainer.find(`[btn_hmwk_${view}] a`).addClass("active"),
                                that.$messagecenterContainer.find(`[btn_cthl_${view}] a`).addClass("active"),
                                that.$messagecenterContainer.find(`[apphomework_${view}]`).addClass("active show"),
                                that.$messagecenterContainer.find(`[appcontroles_${view}]`).addClass("active show"),
                                that.$messagecenterContainer.show(),
                                console.log("need to open message center");
                            }
                        );
                    }
                );                 
            }
/*_closereportcenter*/
        }, {
            key: "_closereportcenter",
            value: function() {
                this.$reportcenterContainer.hide(),
                this.$apiLoader.removeClass("active"),
                console.log("report center closed");
            }
/*_closereportview*/
        }, {
            key: "_closereportview",
            value: function() {
                this.$reportviewContainer.hide(),
                this.$apiLoader.removeClass("active"),
                console.log("report center closed");
            }
/*_viewmonthlyreport*/
        }, {
            key: "_viewmonthlyreport",
            value: function(element, event) {
                var index = this.jQuery(element).data("index"),
                data = this.monthlyreport[index];
                if(data){
                    console.log("data", data);
                    var html = '';
                    html += `<table class="datareport table table-bordered">
                        <tbody>
                          <tr>
                            <th class="text-center" colspan="7" style="font-size: 30px;" >${this._getstring("language_reportview_monthlyreport")}</th>
                          </tr>
                          <tr>
                            <th colspan="3" style="font-size: 20px;">${data?.finaldata?.firstname} ${data?.finaldata?.lastname}</th>
                            <th colspan="4" rowspan="2" class="text-center" style="font-size: 20px;">${this._getstring("language_reportview_monthlyreport_subtitle")}</th>
                          </tr>
                          <tr>
                            <th colspan="3" style="font-size: 20px;">
                              ${this._getstring("language_reportview_monthlyreport_grade")}: ${data?.finaldata?.gradename}<br/>
                              ${this._getstring("language_reportview_monthlyreport_from")}: ${data?.fromdate}<br/>
                              ${this._getstring("language_reportview_monthlyreport_to")}: ${data?.todate}<br/>
                            </th>
                          </tr>
                          <tr class="text-center">
                            <th>${this._getstring("language_reportview_monthlyreport_unit")}</th>
                            <th>${this._getstring("language_reportview_monthlyreport_lesson")}</th>
                            <th>${this._getstring("language_reportview_monthlyreport_totalexcercise")}</th>
                            <th>${this._getstring("language_statusnotmeeting")}</th>
                            <th>${this._getstring("language_statusbasic")}</th>
                            <th>${this._getstring("language_statusgood")}</th>
                            <th>${this._getstring("language_statusexcelent")}</th>
                          </tr>`;
                          data?.finaldata?.reportdata.forEach(function(item,index){
                            console.log("item- ",item);
                            html += `<tr><td rowspan="${item.subtopic.length}">${item?.name}</td>`;
                            item.subtopic.forEach(function(subtopic, subindex){
                                var colordataposition = 0;
                                var color='red';
                                if(subtopic.percent > 85){color = 'blue'; colordataposition=3;}
                                else if(subtopic.percent > 70){color = 'lightgreen';colordataposition=2;} 
                                else if(subtopic.percent > 50){color = 'yellow';colordataposition=1;} 
                                else {color = 'red';colordataposition=0;} 
                                var colordata = `${subtopic.fraction}/${subtopic.maxfraction}<br/><span class="smalldot ${color}"></span>`;
                                if(subindex != 0){
                                    html += `<tr>`;
                                }
                                html += `<td>${subtopic.name}</td>
                                <td class="text-center" >${subtopic.total}</td>
                                <td class="text-center" >${(colordataposition == 0)?colordata:''}</td>
                                <td class="text-center" >${(colordataposition == 1)?colordata:''}</td>
                                <td class="text-center" >${(colordataposition == 2)?colordata:''}</td>
                                <td class="text-center" >${(colordataposition == 3)?colordata:''}</td></tr>`;
                            });
                            html += ``;
                          });
html += `               
                       </tbody>
                      </table>
                `;
                    this.$monthlyreport.html(html),
                    this.$reportviewContainer.show();
                } else {
                    displayToast("Falied", "report", "error");
                }
            }
/*_reportcenter*/
        }, {
            key: "_reportcenter",
            value: function(view="ongoing") {
                var that = this;
                that.$apiLoader.addClass("active"),
                that._APICall(
                    that._prepareRequest(
                        "getMyReport",
                        {
                            lang:that.applang,
                            detailed:1
                        }
                    ),
                    function (result) {
                        that.$apiLoader.removeClass("active"),
                        that._processallreportitems(result.data.allreports),
                        that.$reportcenterContainer.show(),
                        console.log("getMyReport", result)
                    }
                );                 
            }
/*_processallreportitems*/
        },
        {
            key: "_processallreportitems",
            value: function(allitem) {
                var that =  this;
                console.log("_processallreportitems allitem", allitem)
                if(Array.isArray(allitem) && allitem.length > 0 ){
                    this.monthlyreport = allitem;
                    var html = ``;
                    allitem.forEach(function(item,index){
                        html+=`<tr><td>${index+1}</td><td>${item.month}</td><td><span class="btn viewreport btn-startnextquiz" viewmonthlyreport data-index="${index}">${that._getstring("language_reportcenter_table_btnview")}</span></td></tr>`
                    });
                    this.$reporttablebody.html(html);
                } else {
                    this.monthlyreport = [];
                    this.$reporttablebody.html(`<tr><td colspan="3">${that._getstring("language_reportcenter_table_norecord")}</td></tr>`);
                }
            }
/*_togglechangelanguage*/
        },
        {
            key: "_questiontranslation",
            value: function(element) {
                 var that=this;
                console.log('_questiontranslation',element);
                if( that.jQuery(element).hasClass('active')){
                    var currentquestion = that.$qplayer_data.questions[that.$qplayer_question_current];
                    var ques_translations=currentquestion.translation;
                    console.log('ques_translations',ques_translations);
                    that.jQuery('[pophead]').html(that._getstring("language_question_player_translation"));

                    that.jQuery('[poptop]').html(currentquestion.questionTitle+"<br>"+that._prepareQuestion(currentquestion, true));
                    if(ques_translations){
                        that.jQuery('[popbottom]').html(ques_translations.questionTitle+"<br>"+ques_translations.questionText);
                    } else {
                        that.jQuery('[poptop]').addClass("havetranslation");
                        that.jQuery('[popbottom]').html(that._getstring("language_question_player_notranslation"));
                    }
                    that.jQuery("[poptop], [popbottom]").find("img[src^='https://fivestudents.s3'], img[src*='repository_s3bucket']").addClass("latex-s3");
                    that.jQuery('[playerpop]').addClass('active');
                    
                console.log('currentquestion--',currentquestion);
                    console.log('active')
               }
            }
/*_questiontranslation*/
        },
        {
            key: "_questionhints",
            value: function(element) {
                var that=this;
               console.log('_questionhints',element);
               if( that.jQuery(element).hasClass('active')){
                 var currentquestion = that.$qplayer_data.questions[that.$qplayer_question_current];
                that.jQuery('[pophead]').html(that._getstring("language_question_player_hintstext"));
                that.jQuery('[poptop]').html(currentquestion.questionHint?currentquestion.questionHint:that._getstring("language_question_player_nohintstext"));
                if(currentquestion.translation && currentquestion.translation.questionHint){
                    that.jQuery('[popbottom]').html(currentquestion.translation.questionHint);
                    that.jQuery('[poptop]').addClass("havetranslation");
                } else {
                    that.jQuery('[popbottom]').html("");
                    that.jQuery('[poptop]').removeClass("havetranslation");
                }
                that.jQuery("[poptop], [popbottom]").find("img[src^='https://fivestudents.s3'], img[src*='repository_s3bucket']").addClass("latex-s3");
                that.jQuery('[playerpop]').addClass('active');
                console.log('currentquestion--',currentquestion);
               }
              

            }
/*_questionhints*/
        }, {
            key: "_questioncorrection",
            value: function(element) {
                var that=this;
                console.log('_questioncorrection',element);
                if( that.jQuery(element).hasClass('active')){
                    var currentquestion = that.$qplayer_data.questions[that.$qplayer_question_current];
                    console.log('currentquestion--',currentquestion);
                    that.jQuery('[playerpop]').addClass('active');
                    console.log('active');
                    that.jQuery('[pophead]').html(that._getstring("language_question_player_explanation"));
                    that.jQuery('[poptop]').html(currentquestion.generalFeedback?currentquestion.generalFeedback:that._getstring("language_question_player_noexplanation"));
                    if(currentquestion.translation && currentquestion.translation.generalFeedback){
                        that.jQuery('[popbottom]').html(currentquestion.translation.generalFeedback);
                        that.jQuery('[poptop]').addClass("havetranslation");
                    } else {
                        that.jQuery('[popbottom]').html("");
                        that.jQuery('[poptop]').removeClass("havetranslation");
                    }
                    that.jQuery("[poptop], [popbottom]").find("img[src^='https://fivestudents.s3'], img[src*='repository_s3bucket']").addClass("latex-s3");
                    that.jQuery('[playerpop]').addClass('active');
                }
            }
/*_questioncorrection*/
        }, {
            key: "_togglechangelanguage",
            value: function(open) {
                if(this.$changelanguagemodal.hasClass("show")){
                    this.$changelanguagemodal.hide();
                    this.$changelanguagemodal.removeClass("show");
                } else {
                    this.$changelanguagemodal.show();
                    this.$changelanguagemodal.addClass("show");
                }
            }
/*_changelanguage*/
        }, {
            key: "_changelanguage",
            value: function() {
                var lang = "fr";
                switch (this.applang) {
                    case "fr":
                        lang = "ar";
                        break;
                    case "ar":
                        lang = "fr";
                        break;
                }
                this._togglechangelanguage(),
                localStorage.setItem('applang', lang);
                this._checkLogin();
            }
/*_startquiz*/
        }, {
            key: "_startquiz",
            value: function(element, event) {
                if(this.loadedquiz){
                    this.$apiLoader.addClass("active"),
                    this.$qplayer_close_popup.removeClass("active"),
                    this.$qplayer_type = 0,
                    this.$qplayer_finished = 0,
                    this.$qplayerrbtnlist.show(),
                    this._startquizplayer(this.loadedquiz.cmid),
                    console.log("Quiz to start", this.loadedquiz);
                } else {
                    displayToast("Falied", "quiz", "error");
                }
            }
/*_startnextquiz*/
        }, {
            key: "_startnextquiz",
            value: function(element, event) {
                var nextindex = this.loadedquiz_index+1;
                if(this.loadedsubtopic.quizzes[nextindex]){
                    this.loadedquiz = this.loadedsubtopic.quizzes[nextindex],
                    this.loadedquiz_index = nextindex,
                    this.$apiLoader.addClass("active"),
                    this.$qplayer_close_popup.removeClass("active"),
                    this.$qplayer_type = 0,
                    this.$qplayer_finished = 0,
                    this.$qplayerrbtnlist.show(),
                    this._startquizplayer(this.loadedquiz.cmid),
                    console.log("Quiz to start", this.loadedquiz);
                } else {
                    displayToast("Falied", "quiz", "error");
                }
            }
/*_startquizplayer*/
        }, {
            key: "_startquizplayer",
            value: function(moduleid) {
                var that = this;
                this._APICall(
                    this._prepareRequest(
                        "getQuizData",
                        {
                            moduleid:moduleid,
                            lang:this.applang
                        }
                    ),
                    function (result) {
                        if(result.code == 200){
                            that._qplayercloseconfirm();
                            that.$qplayer_close.show();
                            that.$qplayer_pagination.show();
                            that.$qplayer_question_prev.show();
                            that.$qplayer_question_next.show();
                            that.$qplayer_pagination.find(".pagination").show();
                            that.$qplayer.removeClass("haveprogress");
                            that.$qplayer.removeClass("havetimer");
                            if(that.$qplayer_timer){
                                clearInterval(that.$qplayer_timer);
                            }
                            if(that.premiumAccount){
                                that.$quizplayerContainer.removeClass("notpremiumAccount");
                            } else {
                                that.$quizplayerContainer.addClass("notpremiumAccount");
                            }

                            that.$qplayer_data = result.data,
                            that.$qplayer_question_current = 0;
                            that.$qplayer_question_total = result.data.questions.length;
                            that.$quizplayerContainer.attr("subject", result.data.subject),
                            that.$quizplayerContainer.attr("quizlang", result.data.lang),
                            that.$quizplayerContainer.show();
                            that.$qplayer_qplayertimercounter.text('');
                            var attemptid = that.$qplayer_data.current.id;
                            if (sessionStorage.getItem(`at_${attemptid}_current`) !== null) {
                                var lastquestion = sessionStorage.getItem(`at_${attemptid}_current`);
                                console.log("lastquestion- ", lastquestion)
                                if(lastquestion < that.$qplayer_question_total) {
                                    that.$qplayer_question_current = parseInt(lastquestion);
                                }
                            }
                            that._loadquestion();

                            // displayToast("Success", result.message, "success");
                        } else {
                            displayToast("Error", that._getstring("something_went_wrong"), "error");
                        }
                    }
                ); 
            }
/*_starttarlquizplayer*/
        }, {
            key: "_starttarlquizplayer",
            value: function(moduleid) {
                var that = this;
                that.$apiLoader.removeClass("active");
                that.$apiLoader.addClass("active");
                this._APICall(
                    this._prepareRequest(
                        "getTarlQuizData",
                        {
                            moduleid:moduleid,
                            lang:this.applang
                        }
                    ),
                    function (result) {
                        if(result.code == 200){
                            that._qplayercloseconfirm();
                            that.$qplayer_close.hide();
                            that.$qplayer_pagination.hide();
                            if(that.$qplayer_timer){
                                clearInterval(that.$qplayer_timer);
                            }
                            if(that.premiumAccount){
                                that.$quizplayerContainer.removeClass("notpremiumAccount");
                            } else {
                                that.$quizplayerContainer.addClass("notpremiumAccount");
                            }
                            that.$qplayer_finished = 0,
                            that.$qplayer_data = result.data,
                            that.$qplayer_question_current = 0;
                            that.$qplayer_question_total = result.data.questions.length;
                            that.$quizplayerContainer.attr("subject", result.data.subject),
                            that.$quizplayerContainer.attr("quizlang", result.data.lang),
                            that.$quizplayerContainer.show();
                            that.$apiLoader.addClass("active"),
                            that.$qplayer_close_popup.removeClass("active"),

                            that.$qplayer_qplayertimercounter.text('');
                            var attemptid = that.$qplayer_data.current.id;
                            that.$qplayer.removeClass("haveprogress");
                            that.$qplayer.removeClass("havetimer");
                            if(that.$qplayer_data.isTarl && that.$qplayer_data?.isDiagnostic == 0){
                                that.$qplayer.addClass("haveprogress");
                                that.$qplayer_qplayertimerprogress.css("width", `${that.$qplayer_data?.completedPercent}%`);
                            } else {
                                that.$qplayer.removeClass("haveprogress");
                                that.$qplayer_qplayertimerprogress.css("width", `0%`);
                            }
                            if (sessionStorage.getItem(`at_${attemptid}_current`) !== null) {
                                var lastquestion = sessionStorage.getItem(`at_${attemptid}_current`);
                                console.log("lastquestion- ", lastquestion)
                                if(lastquestion < that.$qplayer_question_total) {
                                    that.$qplayer_question_current = parseInt(lastquestion);
                                }
                            }
                            that._loadquestion();
                            that.$apiLoader.removeClass("active");

                            // displayToast("Success", result.message, "success");
                        } else {
                            displayToast("Error", that._getstring("something_went_wrong"), "error");
                        }
                    }
                ); 
            }
/*_checkImmediateFeedback*/
        }, {
            key: "_checkImmediateFeedback",
            value: function() {
                var that = this;
                let currentquestion = this.$qplayer_data.questions[this.$qplayer_question_current+1];
                console.log("_checkImmediateFeedback");
                that.$apiLoader.addClass("active");
                if(that.$qplayer_data.homeworkFilterArea == "2" && that.$qplayer_data.homeworkStudents.includes(that.currentUser.id.toString())){
                    that.$qplayer_question_current = that.$qplayer_question_current+1;
                    that._loadquestion();
                } else {
                    this._APICall(
                        this._prepareRequest(
                            "questionIsBlocked",
                            {
                                homeworkid:this.$qplayer_data?.homeworkId,
                                questionid:currentquestion?.qid
                            }
                        ),
                        function (result) {
                            that.$apiLoader.removeClass("active");
                            if(result.code == 200){
                                if(result.data.blocked){
                                    displayToast("Info", that._getstring("language_isblocked"), "info");
                                } else {
                                    that.$qplayer_question_current = that.$qplayer_question_current+1;
                                    that._loadquestion();
                                }
                            } else {
                                displayToast("Error", that._getstring("something_went_wrong"), "error");
                            }
                        }
                    );
                }
            }
/*_nextquestion*/
        }, {
            key: "_nextquestion",
            value: function(isnext) {
                console.log("isnext- ", isnext);
                this.stopAudioPlaying();
                if(isnext){
                    if(this.$qplayer_data?.immediateFeedback){
                        if(this.$qplayer_question_current+1 < this.$qplayer_question_total){
                            this._checkImmediateFeedback();
                        } else {
                            this._needtosubmitquiz();
                        }
                    } else {
                        if(this.$qplayer_question_current+1 < this.$qplayer_question_total){
                            this.$qplayer_question_current = this.$qplayer_question_current+1;
                            this._loadquestion();
                        } else {
                            this._needtosubmitquiz();
                        }
                    }
                } else {
                    this.$qplayer_question_current = this.$qplayer_question_current-1;
                    this._loadquestion();
                }
            }
/*_needtosubmitquiz*/
        }, {
            key: "_needtosubmitquiz",
            value: function() {
                var that = this;
                // alert("neet to submit quiz");
                if(this.$qplayer_timer){
                    clearInterval(this.$qplayer_timer);
                }
                if(!this.$qplayer_finished){
                    that.$apiLoader.addClass("active");
                    this._APICall(
                        this._prepareRequest(
                            "finishAttempt",
                            {
                                lang:this.applang,
                                finishattempt:1,
                                timeup: 0,
                                attemptid: that.$qplayer_data.current.id
                            }
                        ),
                        function (result) {
                            that._getRewardDetails();
                            console.log("result- ", result);
                            if(result.data.isTarl){
                                if(result.data.nextquiz){
                                    that._starttarlquizplayer(result.data.nextquiz);
                                } else {
                                    displayToast("Success","need to display confirmation page", "success");
                                    that.$confirmationpopuptitle.html(that._getstring(`language_${result.data.successMessage}title`, result.data.successMessageTopic));
                                    that.$confirmationpopupmsg1.html(that._getstring(`language_${result.data.successMessage}1`, result.data.successMessageTopic));
                                    if(result.data.assignedxp){
                                        that.$confirmationpopupmsg1_1.html(that._getstring(`language_${result.data.successMessage}xp`, result.data.assignedxp));
                                    } else {
                                        that.$confirmationpopupmsg1_1.html("");
                                    }

                                    that.$confirmationpopupmsg2.html(that._getstring(`language_${result.data.successMessage}2`, result.data.successMessageTopic));
                                    that.$confirmationpopup.data("action", "loadLevelWorldView");
                                    that.loadLevelWorldViewargs = result.data;
                                    that.$confirmationpopup.show();
                                    that.$apiLoader.removeClass("active");
                                }
                            } else {
                                that.$qplayer_finished = 1;
                                that.$apiLoader.removeClass("active");
                                that.$qplayer_data=null,
                                that.$quizplayerContainer.hide(),
                                that.$apiLoader.addClass("active");
                                if(that.$qplayer_type){that._messagecenter('completed');that._reloadsubtopicdata(0);}
                                else {that._reloadsubtopicdata(1);}
                            }
                        }
                    );
                } else {
                    that.$qplayer_finished = 1;
                    that.$apiLoader.removeClass("active");
                    that.$qplayer_data=null,
                    that.$quizplayerContainer.hide(),
                    that.$apiLoader.addClass("active");
                    if(that.$qplayer_type){that._messagecenter('completed');that._reloadsubtopicdata(0);}
                    else {that._reloadsubtopicdata(1);}
                }                

            }
/*_qplayerclose*/
        }, {
            key: "_qplayerclose",
            value: function() {
                this.stopAudioPlaying();
                this.$qplayer_close_popup.addClass("active");
            }
/*_qplayercloseconfirm*/
        }, {
            key: "_qplayercloseconfirm",
            value: function() {
                if(this.$qplayer_timer){
                    clearInterval(this.$qplayer_timer);
                }
                this.stopAudioPlaying();
                this.$apiLoader.removeClass("active");
                this.$qplayer_data=null;
                this.$quizplayerContainer.hide();
            }
/*_qplayercloseskip*/
        }, {
            key: "_qplayercloseskip",
            value: function() {
                this.$qplayer_close_popup.removeClass("active");
            }
/*_questiontoggle*/
        }, {
            key: "_questiontoggle",
            value: function() {
                this.$qplayer_questiontoggle_text.html(this.$qplayer_question_toggletext);
                this.$qplayer_questiontoggle.addClass("active");
            }
/*_questionhelp*/
        }, {
            key: "_questionhelp",
            value: function(element) {
                console.log('_questionhelp', element);
                let video = this.$qplayer_question_help.data("video");
                console.log("video- ", video);
                var player = document.createElement('video');
                player.setAttribute('controls', ''); // Add controls to the video (play, pause, etc.)
                var source = document.createElement('source');
                source.setAttribute('src', video); // Replace with the path to your video file
                source.setAttribute('type', 'video/mp4');
                player.appendChild(source);
                this.$qplayer_questiontoggle_text.append(player);
                this.$qplayer_questiontoggle.addClass("active");
                player.play();
            }
/*_qplayertoggleremove*/
        }, {
            key: "_qplayertoggleremove",
            value: function() {
                this.$qplayer_questiontoggle_text.html("");
                this.$qplayer_questiontoggle.removeClass("active");
            }
/*_questionsubmit*/
        }, {
            key: "_questionsubmit",
            value: function(skipanswercheck=0) {
                this.stopAudioPlaying();
                var that=this;
                that.$apiLoader.addClass("active");
                //added by dk
                var currentquestion = this.$qplayer_data.questions[this.$qplayer_question_current];
                var q_id = currentquestion.id;
                var q_text = currentquestion.questionText;
                var q_type =   currentquestion.type;
                var q_lang = '';
                var q_answer='';
                var myarray = [];
                var ans_data;
                var validanswer = true;
                console.log("Quesstion type : "+q_type);
                if(this.premiumAccount){
                    if(!currentquestion.isAttempted){
                        switch (q_type) {
                            case "multichoice":
                                this.$qplayer_question_text.find('[data-element="answer"]').each(function(e){
                                    if($(this).prop("checked")){
                                        q_answer = this.value;
                                        if(!q_answer){validanswer = false; }
                                    }
                                });
                                break;
                            case "multiselect":
                                validanswer = true;
                                myarray = [];
                                this.$qplayer_question_text.find('[data-element="answer"]').each(function(e){
                                    if($(this).prop("checked")){
                                        myarray.push(this.value);
                                    }
                                });
                                if(myarray.length == 0){
                                    validanswer = false;
                                }
                                q_answer = myarray;
                                break;
                            case "match":
                                validanswer = true;
                                this.$qplayer_question_text.find('[matchitemrow]').each(function(item){                            
                                    let key, value;
                                    key=that.jQuery(this).find('.txt_match_question_key').val();
                                    value= that.jQuery(this).find('[data-element="answer"]').val();
                                    myarray.push({key, value});                     
                                    if(!value){validanswer = false; }
                                });
                                q_answer = myarray;
                                break;
                            case "truefalse":
                            case "shortanswer":
                            case "numerical":
                                q_answer = this.$qplayer_question_text.find('[data-element="answer"]').val();
                                    if(!q_answer){validanswer = false; }
                                break;
                            case "gapselect":
                                validanswer = true;
                                this.$qplayer_question_text.find('[data-element="answer"]').each(function(e){
                                    let key, value;
                                    key=that.jQuery(this).attr('data-elementkey');
                                    value= that.jQuery(this).val();
                                    if(!value){validanswer = false; }
                                    myarray.push({key, value}); 
                                });
                                q_answer = myarray;
                                break;
                            case "ddwtos":
                                validanswer = true;
                                this.$qplayer_question_text.find('[data-element="answer"]').each(function(e){
                                    let key, value;
                                    key=that.jQuery(this).attr('data-elementkey');
                                    value= that.jQuery(this).find(".drag-item").data("value");
                                    if(!value){validanswer = false; }
                                    myarray.push({key, value}); 
                                });
                                q_answer = myarray;
                                break;
                            case "ddimageortext":
                                validanswer = true;
                                this.$qplayer_question_text.find('[data-element="answer"]').each(function(e){
                                    let key, value;
                                    key=that.jQuery(this).attr('data-elementkey');
                                    value= that.jQuery(this).find(".drag-item").data("value");
                                    if(!value){validanswer = false; }
                                    myarray.push({key, value}); 
                                });
                                q_answer = myarray;
                                // console.log("q_answer- ", q_answer);
                                break;
                            case "wordselect":
                                validanswer = true;
                                console.log("this.$qplayer_question_text htm", this.$qplayer_question_text.html())
                                this.$qplayer_question_text.find('.selcheck').each(function(index, item){
                                    // console.log("Fount index- ", index)
                                    // console.log("Fount item- ", item)
                                    // console.log("Fount item checked- ", item.prop("checked"))
                                    // console.log("Fount item val- ", )
                                    // console.log("Fount item val- ", $(item).val())
                                    let key, value;
                                    if(that.jQuery(item).prop("checked")){
                                        key=that.jQuery(item).attr('name');
                                        value= that.jQuery(item).val();
                                        myarray.push({key, value}); 
                                    }
                                });
                                q_answer = myarray;
                                if(myarray.length == 0){
                                    validanswer=false;
                                }
                                break;
                            case "multianswer":
                                validanswer = true;
                                var allanswer = {};
                                this.$qplayer_question_text.find('[data-element="answer"]').each(function(item){
                                    let key, value;
                                    key=that.jQuery(this).attr('data-elementkey');
                                    value= that.jQuery(this).val();
                                    if(allanswer[key] === undefined){
                                        allanswer[key] = "";
                                    }
                                    if(that.jQuery(this).attr("type")=="radio"){
                                        if(that.jQuery(this).prop("checked")){
                                            allanswer[key] = value;
                                        }
                                    } else if(that.jQuery(this).data("elementtye") == "dropdown") {
                                        allanswer[key] = value;
                                        if(value < 0){validanswer = false; }
                                    } else {
                                        allanswer[key] = value;
                                    }
                                    // console.log("value- ",value)
                                    if(!value){validanswer = false; }
                                });
                                for (const [key, value] of Object.entries(allanswer)) {
                                    myarray.push({key, value}); 
                                }
                                q_answer = myarray;
                                // console.log("q_answer- ", q_answer)
                                // console.log("validanswer- ", validanswer)
                                // that.$apiLoader.removeClass("active");
                                // return;
                                break;
                           default:
                                break;
                        }
                        if(validanswer || skipanswercheck){
                            console.log(q_id+" "+'q_answer',q_answer);
                            that.$apiLoader.addClass("active");
                           this._APICall(
                                this._prepareRequest(
                                    "saveAnswer",
                                    {
                                        lang:this.applang,
                                        wsquestionatmpid:q_id,
                                        wsanswer_data: q_answer,
                                        skipanswercheck: skipanswercheck
                                    }
                                ),
                                function (result) {
                                    // console.log("result- ", result);
                                    if(result.data.question){
                                        that.$qplayer_data.questions[that.$qplayer_question_current] = result.data.question;
                                        if(that.$qplayer_question_current+1 < that.$qplayer_question_total){
                                            // this.$qplayer_question_next.addClass("active");
                                            if(that.$qplayer_data?.homeworkType == "1" || (that.$qplayer_data?.isTarl && that.$qplayer_data?.isDiagnostic != 0)){
                                                that._nextquestion(1);
                                            } else {
                                                that._loadquestion();
                                            }
                                            displayToast("Success","Saved successfully", "success");
                                        } else {
                                            if(that.$qplayer_timer){
                                                clearInterval(that.$qplayer_timer);
                                            }
                                            if(that.$qplayer_data?.homeworkType == "1" || (that.$qplayer_data?.isTarl && that.$qplayer_data?.isDiagnostic != 0)){
                                                that._nextquestion(1);
                                            } else {
                                                that._loadquestion();
                                                displayToast("Success","Need to submit quiz", "warning");
                                            }
                                        }

                                    } else {
                                        that.$apiLoader.removeClass("active");
                                        displayToast("failed","API response error ", "error");
                                    }
                                }
                            );
                        } else {
                            that.$apiLoader.removeClass("active");
                            displayToast("failed","Please answer correctly ", "error");
                        }
                    } else {
                        that.$apiLoader.removeClass("active");
                        if(that.$qplayer_question_current+1 < that.$qplayer_question_total){
                            that._checkImmediateFeedback();
                        } else {
                            that._needtosubmitquiz();
                            // displayToast("Success","submitted successfully", "success");
                            // that.$qplayer_data=null,
                            // that.$quizplayerContainer.hide(),
                            // that.$apiLoader.addClass("active");
                            // if(that.$qplayer_type){that._messagecenter('completed');that._reloadsubtopicdata(0);}
                            // else {that._reloadsubtopicdata(1);}
                        }
                    }
                } else {
                    that.$apiLoader.removeClass("active");
                    that.$qplayer_data=null,
                    that.$quizplayerContainer.hide(),
                    that.$apiLoader.addClass("active");
                    console.log("btest");
                    if(that.$qplayer_type){that._messagecenter('completed');that._reloadsubtopicdata(0);}
                    else {that._reloadsubtopicdata(1);}
                }
            }
/*_formattime*/
        }, {
            key: "_formattime",
            value: function(second) {
                if(second < 0){
                    second = 0;
                }
                // var hours = Math.floor(timeInSeconds / 3600);
                var minutes = Math.floor((second) / 60);
                var seconds = second % 60;
                // hours = hours < 10 ? '0' + hours : hours;
                minutes = minutes < 10 ? '0' + minutes : minutes;
                seconds = seconds < 10 ? '0' + seconds : seconds;
                return minutes + ':' + seconds;
            }
/*_checktimer*/
        }, {
            key: "_checktimer",
            value: function() {
                if(!this.$qplayer_data){
                    return;
                }
                if(this.$qplayer_data.disableTimer){
                    return;
                }
                var currentquestion = this.$qplayer_data.questions[this.$qplayer_question_current];
                var attemptid = this.$qplayer_data.current.id;
                var currentquestionid = currentquestion.id;
                var t = sessionStorage.getItem(`t_${attemptid}_${currentquestionid}_t`);
                var tl = sessionStorage.getItem(`t_${attemptid}_${currentquestionid}_tl`);
                var tp = sessionStorage.getItem(`t_${attemptid}_${currentquestionid}_tp`);
                var tr = sessionStorage.getItem(`t_${attemptid}_${currentquestionid}_tr`);
                if(t && tl){
                    if(!tp){ tp=0; }
                    if(!tr){ tr=tl; }
                    var percent = ((tl-tr)/tl)*100;
                    console.log("tl- ", tl)
                    console.log("tr- ", tr)
                    console.log("tl-tr- ", tl-tr)
                    console.log("percent- ", percent)
                    this.$qplayer_qplayertimerprogress.css("width", `${percent}%`);
                    this.$qplayer_qplayertimercounter.text(this._formattime(tr));
                    if(currentquestion.isAttempted){
                        if(this.$qplayer_timer){
                            clearInterval(this.$qplayer_timer);
                        }
                        this._nextquestion(1);

                        console.log("already attempted- ",this.$qplayer_question_current);
                        return;
                    }
                    tp++;
                    tr--;
                    if(tr < 0) {
                        console.log("already timeup- ",this.$qplayer_question_current);
                        if(this.$qplayer_timer){
                            clearInterval(this.$qplayer_timer);
                        }

                        // currentquestion.isAttempted = true;
                        // this.$qplayer_data.questions[this.$qplayer_question_current].isAttempted = true;
                        // clearTimeout(this.$qplayer_timer);
                        this._questionsubmit(1);
                        return;
                    }
                    sessionStorage.setItem(`t_${attemptid}_${currentquestionid}_tr`, tr);
                    sessionStorage.setItem(`t_${attemptid}_${currentquestionid}_tp`, tp);
                    // setTimeout(() => { console.log("timer started"); this._checktimer(); }, 1000);
                    
                }
            }
/*_checkvideo*/
        }, {
            key: "_checkvideo",
            value: function(qId) {
                console.log("_checkvideo qId- ", qId);
                if(qId) {
                    const videoStatus = sessionStorage.getItem(`stylised-time-progress-generated-video-player-${qId}`);
                    if(videoStatus == 1){
                        console.log("videoStatus1", videoStatus);
                        console.log("qId", qId);
                        this.$qplayer_pagination.show();
                        $(".fullScreenPlayer").removeClass("showPecter");
                        this.$qplayer_question_next.addClass("active");
                        clearInterval(this.$plusplayer_timer);
                    } else {
                        this.$qplayer_pagination.hide();
                        this.$qplayer_question_next.removeClass("active");
                        $(".fullScreenPlayer").addClass("showPecter");
                    }
                } else {
                    clearInterval(this.$plusplayer_timer);
                }
            }
/*_loadquestion*/
        }, {
            key: "_loadquestion",
            value: function() {
                this.$apiLoader.removeClass("active");
                // console.log("this.$qplayer_question_current- ", this.$qplayer_question_current);
                var currentquestion = this.$qplayer_data.questions[this.$qplayer_question_current];
                var questionVideo = this.$qplayer_data.questions;

                var that = this;
                if(currentquestion){
                    if((that.$qplayer_data?.isTarl && that.$qplayer_data?.isDiagnostic != 0 && currentquestion.type != "description") && currentquestion.isAttempted){
                        this._nextquestion(1);
                        return;
                    }
                    var attemptid = this.$qplayer_data.current.id;
                    if(that.$qplayer_data?.isDiagnostic != 1){
                        that.$qplayer_close.show();
                    }
                    var currentquestionid = currentquestion.id;
                    if(!this.$qplayer_data.disableTimer){
                        sessionStorage.setItem(`at_${attemptid}_current`, this.$qplayer_question_current);
                        sessionStorage.setItem(`t_${attemptid}_${currentquestionid}_t`, currentquestion.timed);
                        sessionStorage.setItem(`t_${attemptid}_${currentquestionid}_tl`, currentquestion.timeLimit);
                        if(currentquestion.timed){
                            this.$qplayer.addClass("havetimer");
                            if(this.$qplayer_timer){
                                clearInterval(this.$qplayer_timer);
                            }
                            this.$qplayer_timer = setInterval(() => { that._checktimer(); }, 1000);
                        } else {
                            this.$qplayer.removeClass("havetimer");
                        }
                    } else {
                        this.$qplayer.removeClass("havetimer");
                        if(this.$qplayer_timer){
                            clearInterval(this.$qplayer_timer);
                        }
                    }
                    var questionDataResponse=this._prepareQuestion(currentquestion, false);
                    if(this.$qplayer_data.isTarl){
                        if((that.$qplayer_data?.isTarl && that.$qplayer_data?.isDiagnostic == 0) && currentquestion.isAttempted){
                            that.$qplayer_pagination.show();
                            that.$qplayer_question_prev.hide();
                            that.$qplayer_question_next.show();
                            that.$qplayer_pagination.find(".pagination").hide();
                        } else {
                            that.$qplayer_pagination.hide();
                            that.$qplayer_question_prev.show();
                            that.$qplayer_question_next.show();
                            that.$qplayer_pagination.find(".pagination").show();
                        }
                    }
                    if(!currentquestion.isAttempted && currentquestion.typeVideoUrl){
                        this.$qplayer_question_help.addClass("active");
                        this.$qplayer_question_help.data("video", currentquestion.typeVideoUrl);
                    } else {
                        this.$qplayer_question_help.removeClass("active");
                        this.$qplayer_question_help.data("video", "");
                    }
                    this.$qplayer_question_title.html(currentquestion.questionTitle),
                    this.$qplayer_question_text.html(questionDataResponse),
                    this.$qplayer_question_text.find("img[src^='https://fivestudents.s3'], img[src*='repository_s3bucket']").addClass("latex-s3"),
                    this._mapQuestion(currentquestion),
                    this.$qplayer_pagination_current.text(this.$qplayer_question_current+1),
                    this.$qplayer_pagination_total.text(this.$qplayer_question_total);
                } else {
                    displayToast("Error", "Unable to find question", "error");
                }
            }
/*_mapQuestion*/
        },{
            key:"_mapQuestion",
            value:function(questions){
                var that = this;
                if(questions.type == "ddwtos" || questions.type == "ddimageortext"){
                    var targets = document.querySelectorAll('.drag-container');
                    [].forEach.call(targets, function(target) {
                      that.drag_addTargetEvents(target);
                    });

                    var listItems = document.querySelectorAll('.drag-item');
                    [].forEach.call(listItems, function(item) {
                      that.drag_addEventsDragAndDrop(item);
                    });
                }
                // start description
                if(questions.type == "description") {
                    sessionStorage.setItem(`stylised-time-progress-generated-video-player-${questions.id}`, 0);
                    $('.customvideo').stylise(questions.id);

                    $(".qplayer-body-rightside-bottom .qprev").click(function(){
                         console.log("questions.id", questions.id);
                         sessionStorage.setItem(`stylised-time-progress-generated-video-player-${questions.id}`, 0);
                         $(".fullScreenPlayer").addClass("showPecter");
                         $(".qplayer-body-rightside-bottom .qnext").removeClass("active");
                    });
                    $(".qnext").click(function(){
                         $(".fullScreenPlayer").addClass("showPecter");
                    });
                    if($('.fullScreenPlayer').length > 0){
                        this.$plusplayer_timer = setInterval(() => { that._checkvideo(questions.id); }, 1000);
                    }
                }
                // end description

            }
/*_prepareQuestion*/
        },{
            key:"_prepareQuestion",
            value:function(questions, onlypreview = false){
                var that=this;
                let questionTemplate='';
                let questionVideo='';
                let questionVideoTitle='';
                let questionVideoId='';
                var questionstatus = "";
                questionTemplate+=`${questions.questionText}`;
                switch(questions.type) {
                    case "multianswer":
                        var hastable = questions.questionText.search("table");
                        if(hastable){
                            questionTemplate = questionTemplate.replace(' ','');
                            questionTemplate = questionTemplate.replace('&nbsp;',' ');
                            questionTemplate = questionTemplate.replace('\u00a0',' ');
                            questionTemplate = questionTemplate.replace('  ',' ');
                        }
                        questions.subQuestion.forEach(function(item,index){
                            // console.log('item',item);
                            // console.log('index',index);
                            switch(item.type){
                                case "shortanswer":
                                    var answerstatus="";
                                    var readonly="";
                                    if(item.isAttempted){
                                        readonly="disabled";
                                        const found = item.options.find(element => element.answer == item.userResponse && parseFloat(element.fraction) > 0);
                                       
                                        if(found){
                                            if(questionstatus=="" || questionstatus == "correct"){questionstatus = "correct";} else {questionstatus = "partiallycorrect";}
                                            answerstatus="correct";
                                        } else {
                                            if(questionstatus=="" || questionstatus == "incorrect"){questionstatus = "incorrect";} else {questionstatus = "partiallycorrect";}
                                            answerstatus="incorrect";
                                        }
                                    }
                                    questionTemplate = questionTemplate.replace(`{#${(index+1)}}`,`<input class="${answerstatus}" ${readonly} type="text" name="answer${item.id}" data-elementkey="${item.key}" data-element="answer" data-elementtye="shortanswer" value="${item.userResponse}" autocomplete="off" />`);
                                    break;
                                case "dropdown" :
                                    var optionclass="";
                                    var readonly="";


                                    let options=``;
                                    item.options.forEach(function(option){
                                        var selectedoption = "";
                                        if(item.isAttempted){
                                            readonly="disabled";
                                            if(item.userResponse == option.value){
                                                selectedoption = " selected ";
                                                if(parseFloat(option.fraction) > 0){
                                                    optionclass = "correct";
                                                    if(questionstatus=="" || questionstatus == "correct"){questionstatus = "correct";} else {questionstatus = "partiallycorrect";}
                                                } else {
                                                    if(questionstatus=="" || questionstatus == "incorrect"){questionstatus = "incorrect";} else {questionstatus = "partiallycorrect";}
                                                    optionclass = "incorrect";
                                                }
                                            }
                                        }
                                        options+=`<option ${selectedoption} value="${option.value}">${option.answer}</option>`;
                                    });
                                    options+='</select>';
                                    options=`<select class="${optionclass}" ${readonly} data-elementkey="${item.key}"  name="answer${item.id}" data-element="answer" data-elementtye="dropdown">${options}`;
                                    options+='</select>';
                                    questionTemplate = questionTemplate.replace(`{#${(index+1)}}`,options);
                                    break;
                                case "multichoiceh": 

                                    let checkboxs='';  
                                    item.options.forEach(function(checkbox){
                                        var optionclass="";
                                        var readonly="";
                                        if(item.isAttempted){
                                            readonly="disabled";
                                            // console.log(checkbox.value, parseFloat(checkbox.fraction))
                                            if(item.userResponse == checkbox.value){
                                                if(parseFloat(checkbox.fraction) > 0){
                                                    if(questionstatus=="" || questionstatus == "correct"){questionstatus = "correct";} else {questionstatus = "partiallycorrect";}
                                                    optionclass='correct';
                                                } else {
                                                    if(questionstatus=="" || questionstatus == "incorrect"){questionstatus = "incorrect";} else {questionstatus = "partiallycorrect";}
                                                    optionclass='incorrect';
                                                }
                                            }
                                        }

                                        checkboxs+=`
                                        <label style="display: inline-block;padding-right:10px;">
                                            <input class="${optionclass}" ${readonly} data-elementkey="${item.key}" type="radio"  name="answer${item.id}"  data-element="answer" data-elementtye="multiselect" value="${checkbox.value}"> ${checkbox.answer}
                                        </label>
                                        `;
                                    });
                                    questionTemplate = questionTemplate.replace(`{#${(index+1)}}`,checkboxs);
                                break;
                                case "multichoicev":
                                   let checkboxs1='';  
                                    item.options.forEach(function(multichoicev){

                                        var optionclass="";
                                        var readonly="";
                                        if(item.isAttempted){
                                            readonly="disabled";
                                            // console.log(multichoicev.value, parseFloat(multichoicev.fraction))
                                            if(item.userResponse == multichoicev.value){
                                                if(parseFloat(multichoicev.fraction) > 0){
                                                    if(questionstatus=="" || questionstatus == "correct"){questionstatus = "correct";} else {questionstatus = "partiallycorrect";}
                                                    optionclass='correct';
                                                } else {
                                                    if(questionstatus=="" || questionstatus == "incorrect"){questionstatus = "incorrect";} else {questionstatus = "partiallycorrect";}
                                                    optionclass='incorrect';
                                                }
                                            }
                                        }
                                        checkboxs1+=`
                                        <label style="display: block;padding-right:10px;">
                                            <input class="${optionclass}" ${readonly} data-elementkey="${item.key}" type="radio"  name="answer${item.id}"  data-element="answer" data-elementtye="multiselect" value="${multichoicev.value}"> ${multichoicev.answer}
                                        </label>
                                        `;
                                    });
                                    questionTemplate = questionTemplate.replace(`{#${(index+1)}}`,checkboxs1);
                                break;

                                case "numerical":

                                    var answerstatus="";
                                    var readonly="";
                                    if(item.isAttempted){
                                        readonly="disabled";
                                        const found = item.options.find(element => element.answer == item.userResponse && parseFloat(element.fraction) > 0);
                                       
                                        if(found){
                                            if(questionstatus=="" || questionstatus == "correct"){questionstatus = "correct";} else {questionstatus = "partiallycorrect";}
                                            answerstatus="correct";
                                        } else {
                                            if(questionstatus=="" || questionstatus == "incorrect"){questionstatus = "incorrect";} else {questionstatus = "partiallycorrect";}
                                            answerstatus="incorrect";
                                        }
                                    }

                                    questionTemplate = questionTemplate.replace(`{#${(index+1)}}`,`<input class="${answerstatus}" ${readonly} type="number" name="answer${item.id}" data-elementkey="${item.key}" data-element="answer" data-elementtye="numerical" value="${item.userResponse}" autocomplete="off" />`);
                                break;
                            }
                        });
                        break;
                    case "multiselect":
                        let checkboxs='';
                        questions.options.forEach(function(checkbox){
                            var optionclass="";
                            var readonly="";
                            if(questions.isAttempted){
                                readonly="disabled";
                                var allselected = JSON.parse(questions.userResponse);
                                // console.log("allselected- ", allselected)
                                // console.log("checkbox.value- ", checkbox.value)
                                if(allselected.includes(checkbox.value+"")){
                                    // console.log("checkbox.value found- ", parseFloat(checkbox.fraction))
                                    if(parseFloat(checkbox.fraction) > 0){
                                        if(questionstatus=="" || questionstatus == "correct"){questionstatus = "correct";} else {questionstatus = "partiallycorrect";}
                                        optionclass='correct';
                                    } else {
                                        if(questionstatus=="" || questionstatus == "incorrect"){questionstatus = "incorrect";} else {questionstatus = "partiallycorrect";}
                                        optionclass='incorrect';
                                    }
                                } else if(parseFloat(checkbox.fraction)>0){
                                    if(questionstatus=="" || questionstatus == "incorrect"){questionstatus = "incorrect";} else {questionstatus = "partiallycorrect";}
                                }
                            }
                            checkboxs+=`<div class="checkbox-container">
                                <input type="checkbox" class="${optionclass}" ${readonly} name="multiselect${questions.id}[]"  data-element="answer" data-elementtye="multiselect" value="${checkbox.value}">
                                <div> ${checkbox.answer}</div>
                             </div>`;
                        });
                        questionTemplate+=checkboxs;
                        break;
                    case "multichoice":
                        let radioboxs='';
                        let optionclass='';
                        questions.options.forEach(function(radiobox){
                            var optionclass="";
                            var readonly="";
                            if(questions.isAttempted){
                                readonly="disabled";
                                // console.log(radiobox.value, parseFloat(radiobox.fraction))
                                if(questions.userResponse == radiobox.value){
                                    if(parseFloat(radiobox.fraction) > 0){
                                        if(questionstatus=="" || questionstatus == "correct"){questionstatus = "correct";} else {questionstatus = "partiallycorrect";}
                                        optionclass='correct';
                                    } else {
                                        if(questionstatus=="" || questionstatus == "incorrect"){questionstatus = "incorrect";} else {questionstatus = "partiallycorrect";}
                                        optionclass='incorrect';
                                    }
                                }
                            }
                            radioboxs+=`<div class="multi_choice">
                            <label>
                                <input type="radio" class="${optionclass}" ${readonly} name="multichoice${questions.id}"  data-element="answer" data-elementtye="multichoice" value="${radiobox.value}"> ${radiobox.answer}
                            </label>
                            </div>`;
                              });
                        questionTemplate+=radioboxs;
                        break;
                    case "description":
                        questionVideo+=`${questions.questionVideo}`;
                        questionVideoTitle+=`${questions.questionTitle}`;
                        questionVideoId+=`${questions.id}`;
                        if(questionVideo != ''){
                            var videoTagRegex = /<video\b[^>]*>[\s\S]*?<\/video>/gi;
                            var newQuestionTemplate = questionTemplate.replace(videoTagRegex, '');
                            questionTemplate = newQuestionTemplate; 

                            // Custom Plus Player
                            questionTemplate = `<div class='fullScreenPlayer'><div class="plusplayer"><span class="customvideo" src="${questionVideo}" data-title="${questionVideoTitle}" id="${questionVideoId}"></span></div></div>`;
                        }else{
                            // questionTemplate += questionTemplate;
                        }
                        break;

                    case "truefalse":
                        var truefalseoptions = ``;
                        let tf_readonly='';
                        var tf_optionclass='';
                        questions.options.forEach(function(option){
                            var selectedoption = "";
                            if(questions.isAttempted){
                                tf_readonly="disabled";
                                if(questions.userResponse == option.value){
                                    selectedoption = "selected";
                                    if(parseFloat(option.fraction) > 0){
                                        if(questionstatus=="" || questionstatus == "correct"){questionstatus = "correct";} else {questionstatus = "partiallycorrect";}
                                        tf_optionclass='correct';
                                    } else {
                                        if(questionstatus=="" || questionstatus == "incorrect"){questionstatus = "incorrect";} else {questionstatus = "partiallycorrect";}
                                        tf_optionclass='incorrect';
                                    }
                                }
                            }                            
                            truefalseoptions+=`<option ${selectedoption} value="${option.value}">${option.answer}</option>`;
                        });
                        let truefalse=`<select class="${tf_optionclass}" name="options${questions.id}" ${tf_readonly} data-element="answer" data-elementtye="truefalse">${truefalseoptions}</select>`
                        // console.log('optionsoptions',truefalse);
                         questionTemplate+= truefalse;
                        break;
                    case "match":
                        let match_text='<table class="match_question">';
                        questions.stemOrder.forEach(function(matchitem){
                            var optionclass="";
                            var readonly="";
                            if(questions.isAttempted){
                                readonly="disabled";
                                const found = questions.choiceOrder.find(element => element.value == matchitem.userResponse);
                                if(found){
                                    // console.log("found- ", found)
                                    // console.log("matchitem- ", matchitem)
                                    if(found.answerText == matchitem.answerText){
                                        if(questionstatus=="" || questionstatus == "correct"){questionstatus = "correct";} else {questionstatus = "partiallycorrect";}
                                        optionclass = "correct";
                                    } else {
                                        if(questionstatus=="" || questionstatus == "incorrect"){questionstatus = "incorrect";} else {questionstatus = "partiallycorrect";}
                                        optionclass = "incorrect";
                                    }
                                }
                            }
                            match_text+=`<tr matchitemrow>`;
                            match_text+=`<td><input name="txt_match_question_key${questions.id}" class="txt_match_question_key" type="hidden" value="${matchitem.key}">${matchitem.questionText}</td>`;
                            match_text+=`<td>`;
                            match_text+=`<select class="${optionclass}" ${readonly} name="matchchoice${matchitem.id}" data-element="answer" data-elementtye="match">`;
                            questions.choiceOrder.forEach(function(choiceOrder){
                                var selectedoption = "";
                                if(matchitem.userResponse == choiceOrder.value){
                                    selectedoption = " selected ";
                                }
                                match_text+=`<option ${selectedoption} value="${choiceOrder.value}">${choiceOrder.answerText}</option>`;
                            });
                            match_text+=`</select>`;
                            match_text+=`</td>`;
                            match_text+=`</tr>`;
                        });
                        match_text+=`</table>`;//match_text
                        questionTemplate+=match_text;
                        break; 
                    case "shortanswer":
                        var answerstatus="";
                        var readonly="";
                        if(questions.isAttempted){
                            readonly="disabled";
                            if(questions.rightAnswer == questions.userResponse){
                                if(questionstatus=="" || questionstatus == "correct"){questionstatus = "correct";} else {questionstatus = "partiallycorrect";}
                                answerstatus="correct";
                            } else {
                                if(questionstatus=="" || questionstatus == "incorrect"){questionstatus = "incorrect";} else {questionstatus = "partiallycorrect";}
                                answerstatus="incorrect";
                            }
                        }
                        let shortanswers=`<div><label >Answer: </label><input class="${answerstatus}" ${readonly} type="text" name="answer${questions.id}" data-element="answer" data-elementtye="shortanswer" value="${questions.userResponse}" autocomplete="off" ></div>`;
                        questionTemplate+=shortanswers;
                        break;
                    case "essay":
                        var answerstatus="";
                        var readonly="";
                        if(questions.isAttempted){
                            readonly="disabled";
                            if(questions.rightAnswer == questions.userResponse){
                                if(questionstatus=="" || questionstatus == "correct"){questionstatus = "correct";} else {questionstatus = "partiallycorrect";}
                                answerstatus="correct";
                            } else {
                                if(questionstatus=="" || questionstatus == "incorrect"){questionstatus = "incorrect";} else {questionstatus = "partiallycorrect";}
                                answerstatus="incorrect";
                            }
                        }
                        let essayanswers=`<div><label >Answer: </label><textarea class="${answerstatus}" ${readonly} name="answer${questions.id}" data-element="answer" data-elementtye="textarea" value="${questions.userResponse}" autocomplete="off" ></textarea></div>`;
                        questionTemplate+=essayanswers;
                        break;
                    case "wordselect":
                        if(questions.isAttempted){
                            var hascorrect = questions.questionText.search("selected correctresponse"),
                            hasincorrect = questions.questionText.search("selected incorrectresponse"),
                            hasnotselected = questions.questionText.search("correctposition");
                            if(hascorrect >= 0){
                                questionstatus = "correct";
                            }
                            if(hascorrect >= 0 && (hasincorrect >= 0 || hasnotselected >= 0)){
                                questionstatus = "partiallycorrect";
                            } else if((hasincorrect >= 0 || hasnotselected >= 0)){
                                questionstatus = "incorrect";
                            } else if(hascorrect < 0) {
                                questionstatus = "incorrect";
                            }
                        }
                        break;
                    case "numerical":
                        if(questions.isAttempted){
                            readonly="disabled";
                            if(questions.rightAnswer == questions.userResponse){
                                if(questionstatus=="" || questionstatus == "correct"){questionstatus = "correct";} else {questionstatus = "partiallycorrect";}
                                answerstatus="correct";
                            } else {
                                if(questionstatus=="" || questionstatus == "incorrect"){questionstatus = "incorrect";} else {questionstatus = "partiallycorrect";}
                                answerstatus="incorrect";
                            }
                        }
                        let numerical=`<div><label >Answer: </label><input class="${answerstatus}" type="number" name="answer${questions.id}" data-element="answer" data-elementtye="numerical" value="${questions.userResponse}" autocomplete="off" ></div>`;
                        questionTemplate+=numerical;
                        break;
                    case "ddwtos":
                        let ddwtos_temp=`<div class="dragableoptions">`;
                        var userResponse = [];
                        if(questions.userResponse){
                            userResponse = JSON.parse(questions.userResponse);
                        }
                        questions.options.forEach(function(dditem,index){
                            ddwtos_temp+=`<span class="drag-item" draggable="true" data-value="${dditem.value}" data-text="${dditem.answer}">${dditem.answer}</span>`;
                        });
                        ddwtos_temp+=`</div>`;
                        var dragtocontaioner = "dragtocontaioner";
                        if(questions.isAttempted || onlypreview){
                            ddwtos_temp = "";
                            dragtocontaioner = "";
                        }
                        const regexp = /\[\[[0-9]*]]+/gm;
                        const allelements = [...questionTemplate.matchAll(regexp)];
                        allelements.forEach(function(ddposition,dindex){
                            var answer = dindex+1;
                            var cindex = ddposition[0].replace(/\[/g, "");
                            cindex = cindex.replace(/\]/g, "");
                            // console.log('dindex- ',dindex);
                            // console.log('cindex- ',cindex);
                            var selectedanswer = "";
                            var selectedanswerclass = "";
                            if(questions.isAttempted){

                                var selected = userResponse.find(element => element.key == `p${answer}`);
                                // console.log('userResponse- ',userResponse);
                                // console.log("selected- ", selected);
                                if(selected){
                                    var selectedoption = questions.options.find(element => element.value == selected.value);
                                    // console.log("selectedoption- ", selectedoption);
                                    if(selectedoption){
                                        selectedanswer = selectedoption.answer;
                                        if(selectedoption.order == cindex){
                                            if(questionstatus=="" || questionstatus == "correct"){questionstatus = "correct";} else {questionstatus = "partiallycorrect";}
                                            selectedanswerclass="correct";
                                            // if(parseFloat(selectedoption.fraction) > 0){
                                            // } else {
                                            //     if(questionstatus=="" || questionstatus == "incorrect"){questionstatus = "incorrect";} else {questionstatus = "partiallycorrect";}
                                            //     selectedanswerclass="incorrect";
                                            // }
                                        } else {
                                            if(questionstatus=="" || questionstatus == "incorrect"){questionstatus = "incorrect";} else {questionstatus = "partiallycorrect";}
                                            selectedanswerclass="incorrect";
                                        }
                                    } 
                                }
                            }
                            questionTemplate=questionTemplate.replace(ddposition[0],`<span class="drag-container ${selectedanswerclass}" data-elementkey="p${answer}" data-element="answer">${selectedanswer}</span>`);
                        });


                        questionTemplate=ddwtos_temp+`<div class="${dragtocontaioner}">`+questionTemplate+`</div>`;
                        break;
                    case "gapselect":
                        var userResponse = [];
                        if(questions.userResponse){
                            userResponse = JSON.parse(questions.userResponse);
                        }
                        const gapselect_regexp = /\[[[1-9].]]/g;
                        const allgapselect_positions = [...questionTemplate.matchAll(gapselect_regexp)];
                        console.log("allgapselect_positions", allgapselect_positions);
                        allgapselect_positions.forEach(function(gapposition,dindex){
                            var answer = dindex+1;
                            var cindex = gapposition[0].replace(/\[/g, "");
                            cindex = cindex.replace(/\]/g, "");
                            let gapselect_options=``;
                            if(Array.isArray(questions.options)){
                                var selectedanswerclass = '';
                                var readonlyselect = '';
                                var selected = undefined;
                                if(questions.isAttempted){
                                    console.log("questions: ", questions);
                                    readonlyselect = 'disabled';
                                    var selected = userResponse.find(element => element.key == `p${cindex}`);
                                    if(selected){
                                        console.log("selected: ", selected);
                                        var cseqindex = questions.cseq.findIndex(element => element == cindex);
                                        console.log(`cseqindex: ${cseqindex}, selectedvalue: ${selected.value}`);
                                        if(cseqindex >=0 ){
                                            if((cseqindex+1) == selected.value){
                                                if(questionstatus=="" || questionstatus == "correct"){questionstatus = "correct";} else {questionstatus = "partiallycorrect";}
                                                selectedanswerclass="correct";
                                            } else {
                                                if(questionstatus=="" || questionstatus == "incorrect"){questionstatus = "incorrect";} else {questionstatus = "partiallycorrect";}
                                                selectedanswerclass="incorrect";
                                            }
                                        } 
                                    }
                                }
                                gapselect_options=`<select ${readonlyselect} class="gapselect-inline ${selectedanswerclass}" data-elementkey="p${cindex}"  name="p${cindex}" data-element="answer" data-elementtye="gapselect">`;
                                gapselect_options += `<option value=""></option>`;
                                questions.options.forEach(function(dditem,index){
                                    if(selected && selected.value == dditem.value){
                                        gapselect_options += `<option selected value="${dditem.value}">${dditem.answer}</option>`;
                                    } else {
                                        gapselect_options += `<option value="${dditem.value}">${dditem.answer}</option>`;
                                    }
                                });
                                gapselect_options += `</select>`;
                            }
                            questionTemplate=questionTemplate.replace(gapposition[0],gapselect_options);

                        });
                        break;
                    case "ddimageortext":
                        let ddimage_temp=`<div class="dragableoptions ddimageortext ${questions.multichar?'multicharquestion':''}">`;
                        questions.allDrags.forEach(function(dditem,index){
                            ddimage_temp+=`<div class="drag-item" draggable="true" data-value="${dditem.value}" data-type="${(dditem.file?1:0)}" data-text="${(dditem.file?dditem.file.fileurl:dditem.label)}">${(dditem.file?`<img src="${dditem.file.fileurl}" />`:dditem.label)}</div>`;
                        });
                        ddimage_temp+=`</div>`;
                        var alldrops="";
                        questionstatus=="";
                        questions.allDrops.forEach(function(dditem,index){
                            var answer = index+1;
                            var selectedanswer = " &nbsp; ";
                            var selectedanswerisimage = false;
                            var selectedanswerclass = "";
                            if(questions.isAttempted){
                                var selectedoption = questions.allDrags.find(element => element.value == dditem.userResponse);
                                // console.log("selectedoption- ", selectedoption)
                                // console.log("dditem- ", dditem)
                                if(selectedoption){
                                    selectedanswer = `${(selectedoption.file?`<img src="${selectedoption.file.fileurl}" />`:selectedoption.label)}`;
                                    selectedanswerisimage = `${(selectedoption.file?true:false)}`;
                                    if(selectedoption.no == dditem.choice){
                                        if(questionstatus =="" || questionstatus == "correct"){questionstatus = "correct";} else {questionstatus = "partiallycorrect";}
                                        selectedanswerclass="correct";
                                    } else {
                                        if(questionstatus=="" || questionstatus == "incorrect"){questionstatus = "incorrect";} else {questionstatus = "partiallycorrect";}
                                        selectedanswerclass="incorrect";
                                    }
                                } 
                            }
                            alldrops+=`<span class="imagedrops drag-container ${selectedanswerclass} ${selectedanswerisimage?'selectedimage':''}" drag-container" data-elementkey="p${answer}" data-element="answer" style="top:${dditem.PYTop}%; left:${dditem.PXLeft}%;" >${selectedanswer}</span>`;
                        });
                        var dragtocontaioner = "dragtocontaioner";
                        if(questions.isAttempted || onlypreview){
                            ddimage_temp = "";
                            dragtocontaioner = "";
                        }
                        if(questions.multichar){
                            dragtocontaioner += " multicharquestion ";
                        }

                        // questionTemplate = "";
                        questionTemplate +=`<div class="ddinimage ${(questions.isAttempted?'attempted':'')}"><img src="${questions.backgroundImage}" class="ddcontainer"/>${alldrops}</div>`;
                        questionTemplate=ddimage_temp+`<div class="${dragtocontaioner}">`+questionTemplate+`</div>`;
                        break;
                    default:
                }
                if(this.$qplayer_question_current >0){
                    that.$qplayer_question_prev.addClass("active");
                } else {
                    that.$qplayer_question_prev.removeClass("active");
                }
                if(questions.haveTranslation && !this.$qplayer_data.disableTranslation){
                    this.$qplayer_question_translation.show();
                } else {
                    this.$qplayer_question_translation.hide();
                }
                if(questions.questionHint && !this.$qplayer_data.disableHints){
                    this.$qplayer_question_hints.show();
                    this.$qplayer_question_hints.addClass("active");
                } else {
                    this.$qplayer_question_hints.hide();
                    this.$qplayer_question_hints.removeClass("active");
                }
                this.$qplayer_question_correction.hide();
                if(questions.haveToggle){
                    that.$qplayer_question_toggle.show();
                    that.$qplayer_question_toggletext=questions.toggleQuestion;
                } else {
                    that.$qplayer_question_toggle.hide();
                    that.$qplayer_question_toggletext="";
                }
                if(questions.isAttempted){
                    if(questions.generalFeedback && !this.$qplayer_data.disableExplanation){
                        this.$qplayer_question_correction.show();
                    }
                    if(questions.generalFeedback){
                        this.$qplayer_question_correction.addClass("active");
                    } else {
                        this.$qplayer_question_correction.removeClass("active");
                    }
                    var questionheader = ``;
                    if(this.applang == 'ar'){
                        questionheader = `
                            <div class="questiontextheader">
                                <div class="questiontextheader-qno-score">
                                `+(!that.$qplayer_data.isTarl?`<div class="questiontextheader-qno">${this.$qplayer_question_current+1}/${this.$qplayer_question_total} ${this._getstring("language_question_player_question")}</div>`:``)+`
                                    
                                    <div class="questiontextheader-score">${questions.marks} / ${questions.maxMarks}</div>
                                    <div class="questiontextheader-qno">${this._getstring("language_eventscreen_note")}</div>
                                </div>
                                <div class="questiontextheader-title">${this._getstring("language_question_player_question_"+questionstatus)}</title>
                            </div>
                        `;
                    } else {
                        questionheader = `
                            <div class="questiontextheader">
                                <div class="questiontextheader-qno-score">
                                `+(!that.$qplayer_data.isTarl?`<div class="questiontextheader-qno">${this._getstring("language_question_player_question")} ${this.$qplayer_question_current+1}/${this.$qplayer_question_total} ${this._getstring("language_eventscreen_note")}</div>`:``)+`
                                    <div class="questiontextheader-score">${questions.marks} / ${questions.maxMarks}</div>
                                </div>
                                <div class="questiontextheader-title">${this._getstring("language_question_player_question_"+questionstatus)}</title>
                            </div>
                        `;
                    }
                    if(questions.type != "description"){
                        questionTemplate = questionheader + questionTemplate;
                    }
                    that.$qplayer_question_next.addClass("active");
                    this.$qplayer_question_submit.attr("data-terminate", 0);
                    if(this.$qplayer_question_current+1 == this.$qplayer_question_total){
                        this.$qplayer_question_submit.text(this._getstring("language_question_player_terminate"));
                        this.$qplayer_question_submit.attr("data-terminate", 1);
                        if(!that.$qplayer_data.isTarl){
                            that.$qplayer_question_next.removeClass("active");
                        } else {
                            this.$qplayer_question_submit.hide();
                            that.$qplayer_question_next.addClass("active");
                        }
                        // this._needtosubmitquiz()
                    } else {
                        this.$qplayer_question_submit.hide();
                        this.$qplayer_question_submit.text(this._getstring("language_question_player_save"));
                    }
                    if(!this.premiumAccount){
                        this.$qplayer_question_submit.hide();
                    }
                }else{
                    this.$qplayer_question_submit.attr("data-terminate", 0);
                    this.$qplayer_question_next.removeClass("active");
                    if(!this.premiumAccount){
                        this.$qplayer_question_next.addClass("active");
                        this.$qplayer_question_submit.hide();
                    } else {
                        this.$qplayer_question_submit.show()
                    }
                }

                if(!this.premiumAccount){
                    this.$qplayer_question_submit.hide();
                    ///this.$qplayer_question_current+1 < this.$qplayer_question_total
                } else if((this.$qplayer_question_current+1) == this.$qplayer_question_total){
                    if(!that.$qplayer_data.isTarl){
                        if(questions.isAttempted){
                            this.$qplayer_question_submit.text(this._getstring("language_question_player_terminate"));
                        } else {
                            this.$qplayer_question_submit.text(this._getstring("language_question_player_save"));
                        }
                        this.$qplayer_question_submit.show();
                        this.$qplayer_question_next.removeClass("active");
                    } else {
                        that.$qplayer_question_next.addClass("active");
                        if(questions.isAttempted){
                            this.$qplayer_question_submit.hide();
                        }
                    }

                    ///this.$qplayer_question_current+1 < this.$qplayer_question_total
                } else if(!questions.isAttempted) {
                    this.$qplayer_question_submit.text(this._getstring("language_question_player_save"));
                }
                // console.log('cuuuuQues- ',this.$qplayer_question_current);
                // console.log('ttotalQues- ',this.$qplayer_question_total);
                return questionTemplate;
            }
/*_changequiz*/
        }, {
            key: "_changequiz",
            value: function(element, event) {
                var index = this.jQuery(element).data("index"),
                id = this.jQuery(element).data("id");
                if(this.loadedsubtopic.quizzes[index]){
                    this.jQuery("[changequiz]").removeClass("active"),
                    this.jQuery(element).addClass("active"),
                    this.loadedquiz_index = index,
                    this.loadedquiz = this.loadedsubtopic.quizzes[index],
                    this._quizchangedinsubtopic();
            } else {
                    displayToast("Falied", "failed to change quiz", "error");
                }
            }
/*_quizchangedinsubtopic*/
        }, {
            key: "_quizchangedinsubtopic",
            value: function() {
                var content = "";
                if(this.loadedquiz && this.loadedquiz.lastGrade != ""){
                    content = `<div class="quiz-result" >
                          <div class="quiz-board">
                            <div class="quiz-heading">
                              <div class="quiz-bg">
                                <p>${this.loadedquiz.shortName}</p>
                              </div>
                              <div class="quiz-complete">
                                <p>${this._getquizstatus(this.loadedquiz.status)}</p>
                              </div>
                            </div>
                            <div class="quiz-points">
                              <table class="quiz-points-table">
                                <tr>
                                  <td class="icon"><i class="pointsicon fa fa-star me-2" aria-hidden="true"></i></td>
                                  <td class="desc">Note </td>
                                  <td class="point">
                                    <span class="got">${this.loadedquiz.lastGrade}</span><span class="from">/10</span>
                                  </td>
                                </tr>
                                <tr>
                                  <td class="icon"><img class="pointsicon" src="/assets/images/qpoint-xp.png"/></td>
                                  <td class="desc">XP</td>
                                  <td class="point">
                                    <span class="got">${this.loadedquiz.xp}</span><span class="from">/${this.loadedquiz.maxXp}</span>
                                  </td>
                                </tr>
                              </table>
                            </div>
                          </div>
                        </div>`;

                }
                this.$startquiz.hide(),
                this.$restartquiz.hide(),
                this.$startnextquiz.hide(),
                this.$subtopcresultsection.html(content);
                var random = this._getrandumnumber(1, 4);
                // console.log("this.loadedquiz_index - ",this.loadedquiz_index);
                // console.log("this.loadedsubtopic.quizzes - ",this.loadedsubtopic.quizzes.length);

                if(!this.loadedquiz.locked){
                    if(this.loadedquiz.started){
                        if(this.loadedquiz.completed){
                            this.$restartquiz.show();
                            if((this.loadedquiz_index+1)<=this.loadedsubtopic.quizzes.length){
                                this.$startnextquiz.show();
                            }
                        } else {
                            this.$restartquiz.show();
                        }
                    } else {
                        this.$startquiz.show();
                    }
                }
                // console.log("this.loadedquiz- ", this.loadedquiz)
                // console.log("random- ", random)
                this.$quizlistsectionmessage1.html(this._getstring(`language_eventscreen_pair${random}_message1`))
                this.$quizlistsectionmessage2.html(this._getstring(`language_eventscreen_pair${random}_message2`))
                // console.log("need to reset _quizchangedinsubtopic", this.loadedquiz);
            }
/*_getrandumnumber*/
        }, {
            key: "_getrandumnumber",
            value: function(min, max) {
                min = Math.ceil(min);
                max = Math.floor(max);
                return Math.floor(Math.random() * (max - min) + min);
            }
/*_getquizstatus*/
        }, {
            key: "_getquizstatus",
            value: function(status) {
                var statustext="";
                switch(status) {
                  case 0:
                    statustext = "";
                    // code block
                    break;
                  case 1:
                    statustext = "";
                    // code block
                    break;
                  case 2:
                    statustext = "";
                    // code block
                    break;
                  case 3:
                    statustext = "";
                    // code block
                    break;
                  case 4:
                    statustext = this._getstring("language_eventscreen_quizcompleted");
                    // code block
                    break;
                }
                return statustext;
            }
/*_showframe*/
        }, {
            key: "_showframe",
            value: function(frameid) {
                if(this.currentframe != frameid){
                    this.lastframe = this.currentframe;
                    this.currentframe = frameid;
                }
                this.$form.find(".mainframe").hide();
                this.$form.find("#"+frameid).show();
            }
/*_loadleaderboard*/
        }, {
            key: "_loadleaderboard",
            value: function (timeFrame, boundary) {
                this._showframe("leaderboardviewContainer"),
                    
                // this._requestLeaderboard("all", "national");
                this._requestLeaderboard();                
            }
/*_requestLeaderboard*/
        }, {
            key: "_requestLeaderboard",
            value: function (timeFrame, boundary) {
                this.$apiLoader.addClass("active");
                var that = this;
                 this._APICall(
                    this._prepareRequest(
                        "requestLeaderboard",
                        {
                            timeFrame:that.leaderboard_timeframe,
                            boundary:that.leaderboard_boundry
                        }
                    ),
                     function (result) {
                         // console.log("requestLeaderboard result- ", result.data);
                       //  print
                         let leaderTemplate = '';
                         let userdata = result.data.allRecord;
                         let myRecord = result.data.myRecord;
                         that.$myrankdetails_rank.html(that._getranktodisplay(myRecord.rank));
                         that.$myrankdetails_profile.html(`<span class="leaderpimage" style="background-image: url('${myRecord.image}');"></span>`);
                         that.$myrankdetails_name.html(`<div>${myRecord.charname}</div>`);
                         that.$myrankdetails_score.html(myRecord.final);
                         userdata.forEach(function (item) {
                             leaderTemplate += `<div class="row justify-content-center my-3">                        
                        <div class="col-1">
                          <div class="icon">
                          `+that._getranktodisplay(item.rank)+`
                            <!--<img src="" alt="" width="100%" height="100%">-->
                          </div>
                        </div>
                        <div class="col-1">
                          <div class="profile-wrap">
                            <span class="leaderpimage" style="background-image: url('${item.image}');"></span>
                          </div>
                        </div>
                        <div class="col-7 dotted">
                          <div>${item.charname}</div>
                        </div>
                        <div class="col-2">
                          <div class="result-num">
                            <div class="ribben">
                              <img src="/assets/images/Ae.png" alt="" width="100%" height="100%">
                            </div>
                            <div class="txt">${item.final}</div>
                          </div>
                        </div>
                      </div>`;
                         });
                         $('.leaderboard-content').html(leaderTemplate);
                         that.$apiLoader.removeClass("active")
                        // console.log("requestLeaderboard data result- ", result.data);
                    }
                );
            }
/*_getranktodisplay*/
        }, {
            key: "_getranktodisplay",
            value: function(rank) {
                var rankdata = 0;
                switch(rank) {
                  case 1:
                    rankdata = `<img src="/assets/images/rank-1.png" class="rankholder"/>`;
                    break;
                  case 2:
                    rankdata = `<img src="/assets/images/rank-2.png" class="rankholder"/>`;
                    break;
                  case 3:
                    rankdata = `<img src="/assets/images/rank-3.png" class="rankholder"/>`;
                    break;
                  default:
                    rankdata = rank;
                }
                return `<span class="userrank">${rankdata}</span>`; 
            }
/*_loadprofile*/
        }, {
            key: "_loadprofile",
            value: function() {
                var that = this;
                that.$apiLoader.addClass("active"),
                this._showframe("profileviewContainer"),
                console.log("_logout");
                this._APICall(
                    this._prepareRequest(
                        "getGrades",
                        {
                            lang:this.applang
                        }
                    ),
                    function (result) {
                        if(result.code == 200){
                            if(result.data.allgrade){
                                that.$select_grades.html("");
                                result.data.allgrade.forEach(function(element, index) {
                                    that.$select_grades.append(`<option value="${element.id}">${element.name}</option>`);
                                })
                            }
                            that._updateUserAppearance(),
                            that.$apiLoader.removeClass("active");
                        }
                    }
                );

            }
/*_logout*/
        }, {
            key: "_logout",
            value: function() {
                localStorage.setItem('logintoken', '');
                this._checkLogin(),
                console.log("_logout");
            }
/*_checkLogin*/
        }, {
            key: "_checkLogin",
            value: function() {
                console.log("_checkLogin");
                this.logintoken = localStorage.getItem('logintoken');
                this.applang = localStorage.getItem('applang');
                if(this.logintoken && this.logintoken !=""){
                    if(this.applang){
                        this._reloadapp();
                    } else {
                        this._loadLanguageSelectorView();
                    }
                } else {
                    this._showLogin();
                }
            }
/*_showLogin*/
        }, {
            key: "_showLogin",
            value: function() {
                this._showframe("loginContainer"),
                console.log("_showLogin");
            }
/*_getRewardDetails*/
        }, {
            key: "_getRewardDetails",
            value: function() {
                var that = this;
                this.$form.find("[rightcontent]").hide();
                this._APICall(
                    this._prepareRequest(
                        "getRewardDetails",
                        {
                            lang:this.applang
                        }
                    ),
                    function (result) {
                        if(result.data.currentXp){
                            that.$form.find("[currentXp]").text(result.data.currentXp);
                        }
                        if(result.data.xpLevel){
                            that.$form.find("[xpLevel]").text(result.data.xpLevel);
                        }
                        if(result.data.maxXp){
                            that.$form.find("[maxXp]").text(result.data.maxXp);
                        }
                        that.$form.find("[rightcontent]").show();
                        // console.log("getRewardDetails result- ", result)
                    }
                );
            }
/*_updateUserAppearance*/
        }, {
            key: "_updateUserAppearance",
            value: function() {
                if(this.getMainAccount.currentChild.charImage){
                    this.$src_currentchiltimage.attr("src", this.getMainAccount.currentChild.charImage)
                } else {
                    this.$src_currentchiltimage.attr("src", "/assets/images/demo-profile.png");
                }
                if(this.getMainAccount.currentChild.firstName){
                    this.$txt_firstname.val(this.getMainAccount.currentChild.firstName)
                }
                if(this.getMainAccount.currentChild.lastName){
                    this.$txt_lastname.val(this.getMainAccount.currentChild.lastName)
                }
                if(this.getMainAccount.currentChild.charName){
                    this.$txt_charname.val(this.getMainAccount.currentChild.charName)
                }
                if(this.getMainAccount.currentChild.grade){
                    this.$select_grades.val(this.getMainAccount.currentChild.grade)
                }
            }
/*_updateProfile*/
        }, {
            key: "_updateProfile",
            value: function() {
                var that = this;
                that.$apiLoader.addClass("active");
                this._APICall(
                    this._prepareRequest(
                        "updateChild",
                        {
                            id:this.getMainAccount.currentChild.id,
                            firstname:that.$txt_firstname.val(),
                            lastname:that.$txt_lastname.val(),
                            grade:that.$select_grades.val(),
                            charname:that.$txt_charname.val(),
                            lang:this.applang
                        }
                    ),
                    function (result) {
                        that.$apiLoader.removeClass("active");
                        if(result.code == 200){
                            displayToast("Success", result.message, "success");
                            if(that.$select_grades.val() != that.getMainAccount.currentChild.grade){
                                that._reloadapp();
                            }
                        }
                    }
                );                
            }
/*_loaduser*/
        }, {
            key: "_reloadapp",
            value: function( exammode = false) {
                this._updateLanguageString();
                var that = this;
                this.$qplayer_question_text.removeClass("fr ar"),
                this.jQuery("[poptop]").removeClass("fr ar"),
                this.$qplayer_question_text.addClass(this.applang),
                this.jQuery("[poptop]").addClass(this.applang),

                this._APICall(
                    this._prepareRequest(
                        "getMainAccount",
                        {
                            lang:this.applang
                        }
                    ),
                    function (result) {
                        if(result.code == 200){
                            that.getMainAccount = result.data.userDetails;
                            that.user = result.data.userDetails;
                            that.currentUser = result.data.userDetails.currentChild;

                            // console.log("_reloadapp- ", result.data)
                            // console.log("_reloadapp- that.getMainAccount.currentChild.charImage", that.getMainAccount.currentChild.charImage)
                            if(!that.getMainAccount.currentChild.charImage){
                                that._loadcharacterselectionView();
                            } else if(!that.getMainAccount.currentChild.region){
                                that._loadregionselectionView();
                            } else {
                                that._updateUserAppearance();
                                that._loadWorldView(exammode);
                            }
                        } else {
                            that._showLogin();
                        }
                    }
                );

            }
/*_loadcharacterselectionView*/
        }, {
            key: "_loadcharacterselectionView",
            value: function() {
                var that = this;
                console.log("that getCharacter", that);
                that._showframe("characterselectrionContainer"),
                this.$apiLoader.addClass("active");
                that._APICall(
                    that._prepareRequest(
                        "getCharacter",
                        {
                            lang:that.applang,
                            grade:that.user.currentChild.grade

                        }
                    ),
                    function (result) {
                        that.$apiLoader.removeClass("active");
                        if(result.code == 200){
                            // console.log("_reloadapp- ", result.data.images);
                            var imagepickups = ``;
                            var selectedcharacter = ``;
                            result.data.images.forEach(function(element, index) {
                                var isactive = "";
                                if(element == that.getMainAccount.currentChild.charImage){
                                    selectedcharacter = element;
                                    isactive = "active";
                                }
                                imagepickups+= `<div class="characterthumb ${isactive}" characterthumb>
                      <div class="characterthumb-image">
                        <img src="${element}" class="charimage">
                      </div>
                    </div>`
                            }),
                            that.jQuery("[allcharacters]").html(imagepickups);
                            that.jQuery("[selectedcharacter]").val(selectedcharacter);
                        }
                    }
                );
            }
/*_loadregionselectionView*/
        }, {
            key: "_loadregionselectionView",
            value: function() {
                var that = this;
                that._showframe("regionselectrionContainer"),
                this.$apiLoader.addClass("active");
                that._APICall(
                    that._prepareRequest(
                        "getRegions",
                        {
                            lang:that.applang
                        }
                    ),
                    function (result) {
                        that.$apiLoader.removeClass("active");
                        if(result.code == 200){
                            // console.log("_loadregionselectionView- ", result.data.images);
                            var regionpickups = ``;
                            result.data.data.forEach(function(element, index) {
                                regionpickups+= `<option value="${element}">${element}</option>`
                            }),
                            that.jQuery("[regionselector]").html(regionpickups);
                            that.$reguinselectiondata = result.data.alldata
                            that._updateprovince();
                        }
                    }
                );
            }
/*_checkexammode*/
        }, {
            key: "_checkexammode",
            value: function() {
                if(this.examMode && this.gradedata?.grade){
                    this.$exammode_enabled.removeClass("hidden");
                } else {
                    this.$exammode_enabled.addClass("hidden");
                }
            }
/*_updateprovince*/
        }, {
            key: "_updateprovince",
            value: function() {
                var selectedregion = this.jQuery("[regionselector]").val();
                var selectedregionprovience= this.$reguinselectiondata[selectedregion];
                var provincepickups = ``;
                if(Array.isArray(selectedregionprovience)){
                    selectedregionprovience.forEach(function(element, index) {
                        provincepickups+= `<option value="${element}">${element}</option>`
                    })
                } 
                this.jQuery("[provinceselector]").html(provincepickups);
            }
/*_loadWorldView*/
        }, {
            key: "_loadWorldView",
            value: function(exammode=false) {
                this.$languageselector.removeClass("ar fr");
                this.$languageselector.addClass(this.applang);
                this._getRewardDetails();
                var that = this;
                this.$apiLoader.addClass("active");
                that._APICall(
                    that._prepareRequest(
                        "getGradeData",
                        {
                            lang:that.applang,
                            completetree:true
                        }
                    ),
                    function (result) {
                        that._showframe("worldviewContainer"),
                        that.$apiLoader.removeClass("active");
                        if(that.$qplayer_timer){
                            clearInterval(that.$qplayer_timer);
                        }
                        if(result.code == 200){
                            if(that.$qplayer_timer){
                                clearInterval(that.$qplayer_timer);
                            }
                            that.coursedata=result.data.quest;
                            that.gradedata=result.data;
                            that.examMode = result.examMode;
                            that.loadedexamMode = exammode;
                            that._checkexammode();
                            if(exammode && result.data.grade){
                                that.coursedata=result.data.grade;
                            }
                            var diagcourse = that.coursedata?.courses.find(element => element.requireDiagnostic);
                            if(diagcourse){
                                that.loadedcourse = diagcourse;
                                that._reloadsubtopicdynamicdata(that.loadedcourse.topics);
                                if(diagcourse.diagnosticQuiz){
                                    if(that.loadedcourse?.diagnosticViewed){
                                        that.$confirmationpopuptitle.html(that._getstring(`language_afterdiagvideotitle`));
                                        that.$confirmationpopupmsg1.html(that._getstring(`language_afterdiagvideoline1`));
                                        that.$confirmationpopupmsg2.html("");
                                        that.$confirmationpopup.data("action", "startDiagnosticQuiz");
                                        that.$confirmationpopup.data("diagid", diagcourse.diagnosticQuiz);
                                        that.$confirmationpopup.show();
                                    } else {
                                        console.log("this.loadedcourse- ", that.loadedcourse);
                                        that.$OverviewPlayerbtn.removeClass("active");
                                        sessionStorage.setItem(`stylised-time-progress-generated-video-player-${that.loadedcourse.id}`, 0);

                                        that._showframe("videoOverviewContainer");
                                        that.$OverviewPlayerbtn.data("id", that.loadedcourse?.diagnosticQuiz);
                                        that.$confirmationpopuptitle.html(that._getstring(`language_beforediagvideotitle`));
                                        that.$confirmationpopupmsg1.html(that._getstring(`language_beforediagvideoline1`));
                                        that.$confirmationpopupmsg2.html("");
                                        that.$confirmationpopup.data("action", "playOverviewVideo");
                                        that.$confirmationpopup.show();
                                    }
                                } else if(diagcourse.directIn && diagcourse.directInTopic){
                                    that.loadedtopic = that.loadedtopics[diagcourse.directInTopic];
                                    that._loadtopicView();
                                    that.$apiLoader.removeClass("active");
                                } else {
                                    that._loadWorldViewData();
                                }
                            } else {
                                that._loadWorldViewData();
                            }
                        }
                    }
                );
            }
/*_loadLevelWorldView*/
        }, {
            key: "_loadLevelWorldView",
            value: function(args) {
                this.$languageselector.removeClass("ar fr");
                this.$languageselector.addClass(this.applang);
                var that = this;
                this.$apiLoader.addClass("active");
                that._APICall(
                    that._prepareRequest(
                        "getGradeData",
                        {
                            lang:that.applang,
                            completetree:true,
                            courseid:(args.course?args.course:0)
                        }
                    ),
                    function (result) {
                        if(result.code == 200){
                            if(args?.course && that.coursedata?.courses){
                                let ci = that.coursedata.courses.findIndex(element => element.id == args.course);
                                let cidata = result.data.quest.courses.find(element => element.id == args.course);
                                if(ci !== false){
                                    that.coursedata.courses[ci] = cidata;
                                } else if(cidata){
                                    that.coursedata.courses.push(cidata);
                                } else {
                                    that.coursedata = result.data.quest;
                                }
                            } else {
                                that.coursedata=result.data.quest;
                            }
                            if(args.course){
                                that.loadedcourse = that.coursedata.courses.find(element => element.id == args.course);
                                that._reloadsubtopicdynamicdata(that.loadedcourse.topics);
                                const component = args.componentflow.find(element => element.id == args.component);
                                if(component){
                                    that.loadedtopic = that.loadedtopics[component.parentid];
                                    that._loadtopicView();
                                    that.$apiLoader.removeClass("active");
                                }
                                // if(that.loadedtopic){
                                //     that.loadedtopic = that.loadedcourse.topics.find(element => element.id == that.loadedtopic.id);
                                //     if(that.loadedsubtopic){
                                //         that.loadedsubtopic = that.loadedtopic.subtopics.find(element => element.id == that.loadedsubtopic.id);
                                //     }
                                // }
                            }
                        }
                    }
                );
            }
/*_reloadsubtopicdata*/
        }, {
            key: "_reloadsubtopicdata",
            value: function(reloadtopic=0) {
                var that = this;
                that._APICall(
                    that._prepareRequest(
                        "getGradeData",
                        {
                            lang:that.applang,
                            completetree:true,
                            courseid:(that.loadedcourse?that.loadedcourse.id:0)
                        }
                    ),
                    function (result) {
                        if(result.code == 200){
                            that.coursedata=result.data.quest;
                            if(that.loadedcourse){
                                if(that.$qplayer_timer){
                                    clearInterval(that.$qplayer_timer);
                                }
                                that.loadedcourse = that.coursedata.courses.find(element => element.id == that.loadedcourse.id);
                                that._reloadsubtopicdynamicdata(that.loadedcourse.topics);
                                // if(that.loadedtopic){
                                //     that.loadedtopic = that.loadedcourse.topics.find(element => element.id == that.loadedtopic.id);
                                //     if(that.loadedsubtopic){
                                //         that.loadedsubtopic = that.loadedtopic.subtopics.find(element => element.id == that.loadedsubtopic.id);
                                //     }
                                // }
                            }
                            if(reloadtopic){
                                that._loadsubtopicView();
                            }
                        }
                    }
                );
            }
/*_viewDiagnostic*/
        }, {
            key: "_viewDiagnostic",
            value: function(reloadtopic=0) {
                clearInterval(this.$plusplayer_timer);
                var that = this;
                that._APICall(
                    that._prepareRequest(
                        "viewDiagnostic",
                        {
                            lang:that.applang,
                            courseid: that.loadedcourse.id
                        }
                    ),
                    function (result) {
                    }
                );
            }
/*_reloadsubtopicdynamicdata*/
        }, {
            key: "_reloadsubtopicdynamicdata",
            value: function(subtopics) {
                var that = this;
                subtopics.forEach(function(subtopic, index) {
                    that.loadedtopics[subtopic.id]=subtopic;
                    if(Array.isArray(subtopic.subtopics)){
                        that._reloadsubtopicdynamicdata(subtopic.subtopics);
                    }
                    if(that.loadedtopic && that.loadedtopic.id == subtopic.id){
                        that.loadedtopic = subtopic;
                    }
                    if(that.loadedsubtopic && that.loadedsubtopic.id == subtopic.id){
                        that.loadedsubtopic = subtopic;
                    }

                });
            }
/*_loadWorldViewData*/
        }, {
            key: "_loadWorldViewData",
            value: function() {
                if(this.coursedata){
                    if(this.coursedata.backdropImage.extraLarge){
                        this.$form_SubjectView.css("background-image", 'url("'+this.coursedata.backdropImage.extraLarge+'")');
                    }
                    if(Array.isArray(this.coursedata.courses) && this.coursedata.courses.length > 1){
                        this._loadSubjectViewData();
                    } else {
                        if(this.coursedata.backdropImage.extraLarge){
                            this.$form_WorldView.css("background-image", 'url("'+this.coursedata.backdropImage.extraLarge+'")');
                        }
                        var firstcourse = this.coursedata.courses[0];
                        this.loadedcourse = firstcourse;
                        if(this.loadedcourse?.backdropImage?.extraLarge){
                            this.$form_WorldView.css("background-image", 'url("'+this.loadedcourse?.backdropImage?.extraLarge+'")');
                        }
                        if(firstcourse){
                            this._loadalltopics(),
                            console.log("worldviewContainer completed");
                        } else {
                            displayToast("Error", "Failed to get details, please try after some time", "error");
                        }
                    }
                } else {
                    displayToast("Error", "Failed to get details", "error");
                }
            }
/*_loadSubjectViewData*/
        }, {
            key: "_loadSubjectViewData",
            value: function() {
                if(this.coursedata){
                    if(this.coursedata.backdropImage.extraLarge){
                        this.$form_WorldView.css("background-image", 'url("'+this.coursedata.backdropImage.extraLarge+'")');
                    }
                    this._loadallcourses();
                } else {
                    displayToast("Error", "Failed to get details", "error");
                }
            }
/*_bindappbtns*/
        }, {
            key: "_bindappbtns",
            value: function(btntype, elementid) {
                var that=this;
                // switch(btntype) {
                //   case "course":
                //     this.$form.on("click", "[appbtn"+btntype+elementid+"]", function(e) {that._appbtncourseclicked(this)});
                //     break;
                //   case "topic":
                //     this.$form.on("click", "[appbtn"+btntype+elementid+"]", function(e) {that._appbtntopicclicked(this)});
                //     break;
                //   case "subtopic":
                //     this.$form.on("click", "[appbtn"+btntype+elementid+"]", function(e) {that._appbtnsubtopicclicked(this)});
                //     break;
                //   case "quiz":
                //     this.$form.on("click", "[appbtn"+btntype+elementid+"]", function(e) {that._appbtnquizclicked(this)});
                //     break;
                // }
            }
/*_appbtncourseclicked*/
        }, {
            key: "_appbtncourseclicked",
            value: function(element) {
                var that = this,
                id = this.jQuery(element).data("id"),
                index = this.jQuery(element).data("index");
                this.loadedcourse = this.coursedata.courses[index];
                if(this.loadedcourse?.backdropImage?.extraLarge){
                    this.$form_WorldView.css("background-image", 'url("'+this.loadedcourse?.backdropImage?.extraLarge+'")');
                }
                
                this._loadalltopics();
            }
/*_appbtntopicclicked*/
        }, {
            key: "_appbtntopicclicked",
            value: function(element) {
                console.log("clicked, _appbtntopicclicked")
                var that = this,
                id = this.jQuery(element).data("id"),
                index = this.jQuery(element).data("index");
                this.loadedtopic = this.loadedcourse.topics[index],
                this.loadedtopics[this.loadedtopic.id] = this.loadedtopic;
                this._loadtopicView();
            }
/*_appbtnsubtopicclicked*/
        }, {
            key: "_appbtnsubtopicclicked",
            value: function(element) {
                console.log("clicked, _appbtnsubtopicclicked")
                var that = this,
                id = this.jQuery(element).data("id"),
                index = this.jQuery(element).data("index");
                if(id != this.loadedtopic?.id){
                    let selectedtopic = this.loadedtopic.subtopics[index];
                    if(selectedtopic.status == 3){
                        displayToast("Error", "Not Unlocked Yet", "error");
                        return;
                    } else {
                        this.loadedsubtopic = selectedtopic,
                        this.loadedtopics[this.loadedsubtopic.id] = this.loadedsubtopic;
                        if(this.loadedsubtopic?.isTarl){
                            if(this.loadedsubtopic?.isTarlTopic){
                                if(this.loadedsubtopic?.isTarlQuizId && this.loadedsubtopic?.status != 2){
                                    that._starttarlquizplayer(this.loadedsubtopic?.isTarlQuizId);
                                } else {
                                    displayToast("Error", that._getstring(`language_notyetready`), "error");
                                }
                            } else {
                                this._loadsubtopicView();
                            }
                        } else {
                            this._loadsubtopicView();
                        }
                    }
                }
            }
/*_appbtnquizclicked*/
        }, {
            key: "_appbtnquizclicked",
            value: function(element) {
                console.log("clicked, _appbtnquizclicked")
            }
/*_loadsubtopicView*/
        }, {
            key: "_loadsubtopicView",
            value: function() {
                var that = this;
                that.$apiLoader.addClass("active");
                if(this.loadedsubtopic){
                    if(this.loadedsubtopic.subtopics.length > 0){
                        this.loadedtopic = this.loadedsubtopic;
                        this.loadedtopics[this.loadedtopic.id] = this.loadedtopic;
                        this._loadtopicView();
                    } else {
                        if(this.loadedsubtopic.backdropImage.extraLarge){
                            this.$form_subtopicView.css("background-image", 'url("'+this.loadedsubtopic.backdropImage.extraLarge+'")');
                        }
                        if(this.loadedsubtopic){
                            this.$form_subtopicView.find("[narativename]").text(this.loadedsubtopic.narativeName),
                            this.$form_subtopicView.find("[shortname]").text(this.loadedsubtopic.shortName),
                            this.$subtopicsecondaryimage.attr("src", this.loadedsubtopic.characterImages.secondaryCharacter.imageUrl),
                            this._loadallquizes(),
                            console.log("worldviewContainer completed");
                        }
                        that._showframe("subtopicviewContainer");
                    }
                }
                that.$apiLoader.removeClass("active");
            }
/*_loadtopicView*/
        }, {
            key: "_loadtopicView",
            value: function(element) {
                if(this.loadedtopic){
                    this.$seconddiagnostic.removeClass("active");
                    if(this.loadedtopic?.isTarl){
                        if(this.loadedtopic?.secondDiagBtn && this.loadedtopic?.secondDiag){
                            this.$seconddiagnostic.attr("data-id", this.loadedtopic?.secondDiag);
                            this.$seconddiagnostic.addClass("active");
                        }
                    }
                    if(this.loadedtopic.backdropImage.extraLarge){
                        this.$form_regionView.css("background-image", 'url("'+this.loadedtopic.backdropImage.extraLarge+'")');
                    }
                    if(this.loadedtopic){
                        if(this.loadedtopic.subtopics.length > 1){
                            this.$form_regionView.find("[narativename]").text(this.loadedtopic.narativeName),
                            this.$form_regionView.find("[shortname]").text(this.loadedtopic.shortName),
                            this._loadallsubtopics(),
                            this._showframe("regionviewContainer");
                            // this.$form.find(".mainframe").hide(),
                            // this.$form_regionView.show();
                        } else {
                            this.loadedsubtopic = this.loadedtopic.subtopics[0],
                            this._loadsubtopicView();
                        }
                        console.log("worldviewContainer completed");
                    }
                }
            }
/*_loadallquizes*/
        }, {
            key: "_loadallquizes",
            value: function() {
                var that = this;
                if(this.loadedsubtopic){
                    var currentQuizId = this.loadedsubtopic.currentQuizId;
                    that.$form_subtopicView.find(".quizlist").html(""),
                    this.loadedsubtopic.quizzes.forEach(function(element, index) {
                        if(element.cmid == currentQuizId){ that.loadedquiz = element; that.loadedquiz_index = index; } 
                        else if( !that.loadedquiz){ that.loadedquiz = element; that.loadedquiz_index = index; }
                        if(that.loadedquiz.topicId != element.topicId){ that.loadedquiz = element; that.loadedquiz_index = index; }
                        that.$form_subtopicView.find(".quizlist").append(that._generatequizbutton(element, index));
                    });
                }
            }
/*_loadallcourses*/
        }, {
            key: "_loadallcourses",
            value: function() {
                var that = this;
                if(this.coursedata){
                    var btntype = 'subject';
                    that.$form_SubjectView.find("[subjectcontainer]").html("");
                    if(this.coursedata.backdropImage.extraLarge){
                        this.$form_SubjectView.css("background-image", 'url("'+this.coursedata.backdropImage.extraLarge+'")');
                    }
                    this.coursedata.courses.forEach(function(element, index) {
                        btntype = element.type,
                        that.$form_SubjectView.find("[subjectcontainer]").append(that._generatebutton(element, index)),
                        that._bindappbtns(btntype, element.id);
                    }),
                    that._showframe("subjectviewContainer");
                }
            }
/*_loadcoursetitle*/
        }, {
            key: "_loadcoursetitle",
            value: function() {
                var that = this;
                if(this.loadedcourse){
                    this.$form_WorldView.find("[narativename]").text(this.loadedcourse.narativeName),
                    this.$form_WorldView.find("[shortname]").text(this.loadedcourse.shortName),
                    this.$coursetitlebg.attr("src", `/assets/images/tp_${this.loadedcourse.subject}_title.png`),
                    this.$coursesubtitlebg.attr("src", `/assets/images/tp_${this.loadedcourse.subject}_sd.png`),
                    this.$form_WorldView.find("[narativename]").attr("class", this.loadedcourse.subject),
                    this.$form_WorldView.find("[shortname]").attr("class", this.loadedcourse.subject);
                }
            }
/*_loadalltopics*/
        }, {
            key: "_loadalltopics",
            value: function() {
                var that = this;
                if(this.loadedcourse){
                    if(this.loadedcourse?.requireDiagnostic && this.loadedcourse?.diagnosticQuiz){
                        if(this.loadedcourse?.diagnosticViewed){
                            that._starttarlquizplayer(this.loadedcourse?.diagnosticQuiz);
                        } else {
                            console.log("this.loadedcourse- ", that.loadedcourse);
                            that.$OverviewPlayerbtn.removeClass("active");
                            sessionStorage.setItem(`stylised-time-progress-generated-video-player-${that.loadedcourse.id}`, 0);

                            that._showframe("videoOverviewContainer");
                            that.$OverviewPlayerbtn.data("id", that.loadedcourse?.diagnosticQuiz);
                            that.$confirmationpopuptitle.html(that._getstring(`language_beforediagvideotitle`));
                            that.$confirmationpopupmsg1.html(that._getstring(`language_beforediagvideoline1`));
                            that.$confirmationpopupmsg2.html("");
                            that.$confirmationpopup.data("action", "playOverviewVideo");
                            that.$confirmationpopup.show();
                        }
                    } else {
                        that.$OverviewPlayerbtn.removeClass("active");
                        var btntype = 'course';
                        var starts = this.wordviewtopic_page * that.btnlimit;
                        var ends = starts + that.btnlimit;
                        if(ends < this.loadedcourse.topics.length){
                            this.$topicnextpage.show();
                        } else {
                            this.$topicnextpage.hide();
                        }
                        if(starts > 0){
                            this.$topicprevpage.show();
                        } else {
                            this.$topicprevpage.hide();
                        }
                        this.$form_WorldView.find("[topiccontainer]").html(""),
                        this._loadcoursetitle(),
                        this.loadedcourse.topics.forEach(function(element, index) {
                            if(index >= starts && index <= ends){
                                btntype = element.type,
                                that.$form_WorldView.find("[topiccontainer]").append(that._generatebutton(element, index)),
                                that._bindappbtns(btntype, element.id);
                            }
                        }),
                        that._showframe("worldviewContainer");
                    }
                }
            }
/*_checkDiagnosticVideo*/
        }, {
            key: "_checkDiagnosticVideo",
            value: function(qId) {
                var that = this;
                if(qId) {
                    const videoStatus = sessionStorage.getItem(`stylised-time-progress-generated-video-player-${qId}`);
                    if(videoStatus == 1){
                        console.log("d videoStatus1", videoStatus);
                        console.log("d qId", qId);
                        that.$OverviewPlayerbtn.addClass("active");
                        $(".fullScreenPlayer").removeClass("showPecter");
                        
                    }else{
                        that.$OverviewPlayerbtn.removeClass("active");
                        $(".fullScreenPlayer").addClass("showPecter");
                    }
                } else {
                    clearInterval(this.$plusplayer_timer);
                }
            }
/*_loadallsubtopics*/
        }, {
            key: "_loadallsubtopics",
            value: function() {
                var that = this;
                if(this.loadedtopic){
                    var btntype = '';
                    var starts = this.regionviewsubtopic_page * that.btnlimit;
                    var ends = starts +that.btnlimit;
                    if(ends < this.loadedtopic.subtopics.length){
                        this.$subtopicnextpage.show();
                    } else {
                        this.$subtopicnextpage.hide();
                    }
                    if(starts > 0){
                        this.$subtopicprevpage.show();
                    } else {
                        this.$subtopicprevpage.hide();
                    }
                    console.log("this.regionviewsubtopic_page: ", this.regionviewsubtopic_page)
                    console.log("that.btnlimit: ", that.btnlimit)
                    console.log("starts: ", starts)
                    console.log("ends: ", ends)
                    that.$form_regionView.find("[subtopiccontainer]").html(""),
                    console.log('this.loadedtopic.subtopics------- ',this.loadedtopic.subtopics);
                    this.loadedtopic.subtopics.forEach(function(element, index) {
                        if(index >= starts && index < ends){
                            btntype = element.type,
                            that.$form_regionView.find("[subtopiccontainer]").append(that._generatebutton(element, index));
                            that._bindappbtns(btntype, element.id);
                        }
                    });
                }
            }
/*_generatebutton*/
        }, {
            key: "_generatebutton",
            value: function(element, index) {
                if(element.type == "course"){
                    return '<div class="appbtn appbtn'+element.type+' appbtn_'+element.subject+' " appbtn'+element.type+' data-id="'+element.id+'" data-index="'+index+'" style="left:'+element.xPos+'%; top:'+element.yPos+'%;"><img class="course-bg" src="/assets/images/'+element.subject+'_banner.png"><span class="appbtn-text">'+element.narativeName+'</span><span class="appbtn-subject">'+this._getstring(`language_${element.subject}`)+'</span></div>';
                } else {
                    return '<div class="appbtn appbtn'+element.type+' " appbtn'+element.type+' data-id="'+element.id+'" data-index="'+index+'" style="left:'+element.xPos+'%; top:'+element.yPos+'%;">'+(element.isTarl && element.status==1?'<img class="appbtn-icon playicon" src="/assets/images/play.png">':'<img class="appbtn-icon" src="/assets/images/'+element.type+'_status'+element.status+'.png">')+'<span class="appbtn-text">'+element.narativeName+'</span></div>';
                }
            }
/*_generatequizbutton*/
        }, {
            key: "_generatequizbutton",
            value: function(element, index) {
                /*Locked*/
                var active = "";
                if(this.loadedquiz.id == element.id){
                    this.loadedquiz = element;
                    active = "active";
                    this._quizchangedinsubtopic();
                }
                var quizstatusimg = `/assets/images/quiz_status${element.status}.png`;
                var lastgrade = "";
                if(element.lastGrade != ""){
                    lastgrade = `<p class="quizscore">${element.lastGrade}</p>`;

                }
                return `<div class="quizbtn ${active}" changequiz data-index="${index}" data-id="${element.id}" ><img src="${quizstatusimg}" class="icon">${lastgrade}<p class="quizname">${element.narativeName}</p></div>`;
                // return '<div class="appbtn appbtn'+element.type+' " appbtn'+element.type+' data-id="'+element.id+'" data-index="'+index+'" style="left:'+element.xPos+'%; top:'+element.yPos+'%;"><img class="appbtn-icon" src="/assets/images/topic-btn-icon.png"><span class="appbtn-text">'+element.narativeName+'</span></div>';
            }
/*_loadLanguageSelectorView*/
        }, {
            key: "_loadLanguageSelectorView",
            value: function() {
                this._showframe("langContainer"),
                console.log("_loadLanguageSelectorView");
            }
/*drag_addTargetEvents*/
        }, {
            key: "drag_addTargetEvents",
            value: function(target) {
                target.addEventListener('dragover', dragOver, false),
                target.addEventListener('dragenter', dragEnter, false),
                target.addEventListener('dragleave', dragLeave, false),
                target.addEventListener('drop', dragDrop, false);
            }
/*drag_addEventsDragAndDrop*/
        }, {
            key: "drag_addEventsDragAndDrop",
            value: function(el) {
                // console.log("drag_addEventsDragAndDrop- ", el),
                el.addEventListener('dragstart', dragStart, false),
                el.addEventListener('dragend', dragEnd, false),
                el.addEventListener('touchstart', touchStart, false),
                el.addEventListener('touchmove', touchMove, false),
                el.addEventListener('touchend', touchEnd, false);
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
                        if(data.code == 100){
                            that.$apiLoader.removeClass("active");
                            localStorage.setItem('logintoken', '');
                            that._showLogin();
                            // displayToast(data.error.title, data.error.message, "error");
                        } else if(data.code != 200){
                            that.$apiLoader.removeClass("active");
                            displayToast(data.error.title, data.error.message, "error");
                        } else {
                            that.premiumAccount = data.premiumAccount;
                            that.premiumAccountExpiry = data.premiumAccountExpiry;
                            that.remainingDays = data.remainingDays;
                            success(data);
                        }
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
        t("#mainapp").each(function() {
            (new AppController).init(t, t(this))
        }), window.errors && window.errors.length && e.showMessage("Please correct the following errors:", window.errors)
    })
}(jQuery);
