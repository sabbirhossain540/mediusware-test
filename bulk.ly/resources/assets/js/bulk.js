$('.navbar-fixed-top .navbar-brand').on('click', function() {
	$('#app').toggleClass('nav-toggle');
});
$('.left-navbar .dropdown .toggle').each(function() {
	$(this).click(function() {
		$('.left-navbar .dropdown .toggle').not(this).parent().find('.dropdown-menu').slideUp();;
		$(this).parent().find('.dropdown-menu').slideToggle();
	});
});

function ajaxStart() {
	$('.ajax-working').fadeIn();
}

function ajaxEnd() {
	$('.ajax-working').fadeOut();
}
$('input[name=quantity]').change(function() {
	$('.quan').text($(this).val());
});
var rng = document.querySelector("input[name=quantity]");
if (rng) {
	var read = function read(evtType) {
		rng.addEventListener(evtType, function() {
			window.requestAnimationFrame(function() {
				$('.quan').text(rng.value);
			});
		});
	};
	read("mousedown");
	read("mousemove");
}

function objectifyForm(formArray) {
	var returnArray = {};
	for (var i = 0; i < formArray.length; i++) {
		returnArray[formArray[i]['name']] = formArray[i]['value'];
	}
	return returnArray;
}

function formatAMPM(date) {
	var hours = date.getHours();
	var minutes = date.getMinutes();
	var ampm = hours >= 12 ? 'pm' : 'am';
	hours = hours % 12;
	hours = hours ? hours : 12; // the hour '0' should be '12'
	minutes = minutes < 10 ? '0' + minutes : minutes;
	var strTime = hours + ':' + minutes + ' ' + ampm;
	return strTime;
}

function timeConverter(unix) {
	var a = new Date(unix * 1000);
	var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
	var year = a.getFullYear();
	var month = months[a.getMonth()];
	var date = a.getDate();
	var hour = a.getHours();
	var min = a.getMinutes();
	var time = date + ' ' + month + ' ' + year + ', ' + formatAMPM(a);
	return time;
}
$('[data-sent]').each(function() {
	var sent = $(this).data('sent');
	if (sent) {
		var ago = timeDifference(Date.now(), sent * 1000);
		var sentdate = $(this).text(ago);
	} else {
		var sentdate = $(this).text('Never sent');
	}
});

function timeDifference(current, previous) {
	var msPerMinute = 60 * 1000;
	var msPerHour = msPerMinute * 60;
	var msPerDay = msPerHour * 24;
	var msPerMonth = msPerDay * 30;
	var msPerYear = msPerDay * 365;
	var elapsed = current - previous;
	if (elapsed < msPerMinute) {
		var second = Math.round(elapsed / 1000);
		if (second == '1') {
			return second + ' second ago';
		} else {
			return second + ' seconds ago';
		}
	} else if (elapsed < msPerHour) {
		var minute = Math.round(elapsed / msPerMinute);
		if (minute == '1') {
			return minute + ' minute ago';
		} else {
			return minute + ' minutes ago';
		}
	} else if (elapsed < msPerDay) {
		var hour = Math.round(elapsed / msPerHour);
		if (hour == '1') {
			return hour + ' hour ago';
		} else {
			return hour + ' hours ago';
		}
	} else if (elapsed < msPerMonth) {
		var day = Math.round(elapsed / msPerDay);
		if (day == '1') {
			return day + ' day ago';
		} else {
			return day + ' days ago';
		}
	} else if (elapsed < msPerYear) {
		var month = Math.round(elapsed / msPerMonth);
		if (month == '1') {
			return month + ' month ago';
		} else {
			return month + ' months ago';
		}
	} else {
		var year = Math.round(elapsed / msPerYear);
		if (year == '1') {
			return year + ' year ago';
		} else {
			return year + ' years ago';
		}
	}
}
$('input[name=period]').change(function() {
	var period = $(this).val();
	$('.levels').hide();
	$('.levels.' + period).show();
});
$('input[name=level]').change(function() {
	$('input[name=level]').parent().removeClass('active focus');
	$(this).parent().addClass('active focus');
});
$(window).on('load', function() {
	$('.group-col').each(function() {
		var lengthGroup = $(this).find('.group-single').length;
		$(this).find('.count').text('(' + lengthGroup + ')');
	});
});
var $update_user_form = $('#update-user');
$update_user_form.find('button[type=submit]').click(function(event) {
	ajaxStart();
	var data = objectifyForm($update_user_form.serializeArray());
	$.ajax({
		type: "POST",
		url: '/update-user',
		data: data,
		success: function success(msg) {
			if (msg == 1) {
				$update_user_form.parents('.panel-body').prepend('<div class="alert alert-success" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Updated</div>');
			} else {
				$.each(msg, function(index, value) {
					$update_user_form.parents('.panel-body').prepend('<div class="alert alert-info" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + value + '</div>');
				});
			}
			ajaxEnd();
		},
		error: function error(xhr, ajaxOptions, thrownError) {
			if (xhr.status) {
				ajaxEnd();
			}
		}
	});
	return false;
});
var $upgrade_plan_form = $('#upgrade-plan');
$upgrade_plan_form.find('button[type=submit]').click(function(event) {
	ajaxStart();
	var data = objectifyForm($upgrade_plan_form.serializeArray());
	$.ajax({
		type: "POST",
		url: '/upgrade-plan',
		data: data,
		success: function success(msg) {
			ajaxEnd();
		},
		error: function error(xhr, ajaxOptions, thrownError) {
			if (xhr.status) {
				ajaxEnd();
			}
		}
	});
	return false;
});
// 
var $ctgp = $('#csv-to-groups-posts');
$ctgp.find('button[type=submit]').click(function(event) {
	//ajaxStart();
	var data = objectifyForm($ctgp.serializeArray());
	var file = $ctgp.find('input[type=file]');
	var csv = file[0].files;
	data.file = csv[0];
	data.name = csv[0].name;
	var ext = file.val().split('.').pop().toLowerCase();
	if ($.inArray(ext, ['csv']) == -1) {
		alert('Whoops! Please try saving your file as a CSV and upload again');
		return false;
	}
	$.ajax({
		url: '/csv-to-groups-posts',
		type: 'POST',
		data: data,
		async: true,
		cache: false,
		contentType: false,
		processData: false,
		success: function success(returndata) {},
		error: function error(xhr, ajaxOptions, thrownError) {}
	});
	return false;
});
// csv-to-content-upload
var $ctcu = $('#csv-to-content-upload');
$ctcu.find('input[name=csv]').change(function() {
	$ctcu.submit();
});
$ctcu.submit(function(event) {
	var file = $ctcu.find('input[type=file]');
	var csv = file[0].files;
	var ext = file.val().split('.').pop().toLowerCase();
	if ($.inArray(ext, ['csv']) == -1) {
		alert('Whoops! Please try saving your file as a CSV and upload again');
		return false;
	}
	ajaxStart();
	var data = new FormData();
	data.append('_token', $ctcu.find('input[name=_token]').val());
	data.append('file', csv[0]);
	data.append('modified', csv[0].lastModified);
	data.append('name', csv[0].name);
	$.ajax({
		url: '/csv-to-content-upload',
		type: 'POST',
		data: data,
		async: true,
		cache: false,
		contentType: false,
		processData: false,
		success: function success(returndata) {
			console.log(returndata);
			window.location.replace(window.Laravel.APP_URL + '/content-upload/pending/' + returndata);
			ajaxEnd();
		},
		error: function error(xhr, ajaxOptions, thrownError) {
			if (xhr.status) {
				ajaxEnd();
			}
		}
	});
	return false;
});
// csv-to-curation-upload
var $ctcuru = $('#csv-to-curation-upload');
$ctcuru.find('input[name=csv]').change(function() {
	$ctcuru.submit();
});
$ctcuru.submit(function(event) {
	var file = $ctcuru.find('input[type=file]');
	var csv = file[0].files;
	var data = new FormData();
	data.append('_token', $ctcuru.find('input[name=_token]').val());
	data.append('file', csv[0]);
	data.append('modified', csv[0].lastModified);
	data.append('name', csv[0].name);
	var ext = file.val().split('.').pop().toLowerCase();
	if ($.inArray(ext, ['csv']) == -1) {
		alert('Whoops! Please try saving your file as a CSV and upload again');
		return false;
	}
	ajaxStart();
	$.ajax({
		url: '/csv-to-curation-upload',
		type: 'POST',
		data: data,
		async: true,
		cache: false,
		contentType: false,
		processData: false,
		success: function success(returndata) {
			console.log(returndata);
			window.location.replace(window.Laravel.APP_URL + '/content-curation/pending/' + returndata);
			ajaxEnd();
		},
		error: function error(xhr, ajaxOptions, thrownError) {
			if (xhr.status) {
				ajaxEnd();
			}
		}
	});
	return false;
});
// csv-to-rss-automation-upload
var $ctrauu = $('#csv-to-rss-automation-upload');
$ctrauu.find('input[name=csv]').change(function() {
	$ctrauu.submit();
});
$ctrauu.submit(function(event) {
	var file = $ctrauu.find('input[type=file]');
	var csv = file[0].files;
	var data = new FormData();
	data.append('_token', $ctrauu.find('input[name=_token]').val());
	data.append('file', csv[0]);
	data.append('modified', csv[0].lastModified);
	data.append('name', csv[0].name);
	var ext = file.val().split('.').pop().toLowerCase();
	if ($.inArray(ext, ['csv']) == -1) {
		alert('Whoops! Please try saving your file as a CSV and upload again');
		return false;
	}
	ajaxStart();
	$.ajax({
		url: '/csv-to-rss-automation-upload',
		type: 'POST',
		data: data,
		async: true,
		cache: false,
		contentType: false,
		processData: false,
		success: function success(returndata) {
			window.location.replace(window.Laravel.APP_URL + '/rss-automation/pending/' + returndata);
			ajaxEnd();
		},
		error: function error(xhr, ajaxOptions, thrownError) {
			if (xhr.status) {
				ajaxEnd();
			}
		}
	});
	return false;
});
// content-online
var $aco = $('#add-content-online');
$aco.find('button[type=submit]').click(function(event) {
	ajaxStart();
	var data = objectifyForm($aco.serializeArray());
	$.ajax({
		type: "POST",
		url: '/add-content-online',
		data: data,
		success: function success(msg) {
			window.location.replace(window.Laravel.APP_URL + '/content-upload/pending/' + msg);
			ajaxEnd();
		},
		error: function error(xhr, ajaxOptions, thrownError) {
			if (xhr.status) {
				ajaxEnd();
			}
		}
	});
	return false;
});
// curation-online
var $acuro = $('#add-curation-online');
$acuro.find('button[type=submit]').click(function(event) {
	ajaxStart();
	var data = objectifyForm($acuro.serializeArray());
	$.ajax({
		type: "POST",
		url: '/add-curation-online',
		data: data,
		success: function success(msg) {
			window.location.replace(window.Laravel.APP_URL + '/content-curation/pending/' + msg);
			ajaxEnd();
		},
		error: function error(xhr, ajaxOptions, thrownError) {
			if (xhr.status) {
				ajaxEnd();
			}
		}
	});
	return false;
});
// rss-automation-online
var $arao = $('#add-rss-automation-online');
$arao.find('button[type=submit]').click(function(event) {
	ajaxStart();
	var data = objectifyForm($arao.serializeArray());
	$.ajax({
		type: "POST",
		url: '/add-rss-automation-online',
		data: data,
		success: function success(msg) {
			window.location.replace(window.Laravel.APP_URL + '/rss-automation/pending/' + msg);
			ajaxEnd();
		},
		error: function error(xhr, ajaxOptions, thrownError) {
			if (xhr.status) {
				ajaxEnd();
			}
		}
	});
	return false;
});
// content-online-ingroup
var $acog = $('#add-content-online-ingroup');
$acog.find('button[type=submit]').click(function(event) {
	ajaxStart();
	var data = objectifyForm($acog.serializeArray());
	$.ajax({
		type: "POST",
		url: '/add-content-online-ingroup',
		data: data,
		success: function success(msg) {
			ajaxEnd();
			location.reload();
		},
		error: function error(xhr, ajaxOptions, thrownError) {
			if (xhr.status) {
				ajaxEnd();
			}
		}
	});
	return false;
});
var $timezone_form = $('#timezone-form');
$timezone_form.find('button[type=button]').click(function(event) {
	ajaxStart();
	var data = objectifyForm($(this).parents('form').serializeArray());
	$.ajax({
		type: "POST",
		url: '/timezone-form',
		data: data,
		success: function success(msg) {
			ajaxEnd();
		},
		error: function error(xhr, ajaxOptions, thrownError) {
			if (xhr.status) {
				ajaxEnd();
			}
		}
	});
	return false;
});
// curation-online-ingroup
$(window).on('load', function() {
	var $acurog = $('.add-curation-online-ingroup');
	$acurog.each(function() {
		$(this).find('button[type=submit]').click(function(event) {
			ajaxStart();
			var data = objectifyForm($(this).parents('form').serializeArray());
			$.ajax({
				type: "POST",
				url: '/add-curation-online-ingroup',
				data: data,
				success: function success(msg) {
					ajaxEnd();
					location.reload();
				},
				error: function error(xhr, ajaxOptions, thrownError) {
					if (xhr.status) {
						ajaxEnd();
					}
				}
			});
			return false;
		});
	});
});
// rss-automation-online-ingroup
var $araoi = $('#add-rss-automation-online-ingroup');
$araoi.find('button[type=submit]').click(function(event) {
	ajaxStart();
	var data = objectifyForm($araoi.serializeArray());
	$.ajax({
		type: "POST",
		url: '/add-rss-automation-online-ingroup',
		data: data,
		success: function success(msg) {
			ajaxEnd();
			location.reload();
		},
		error: function error(xhr, ajaxOptions, thrownError) {
			if (xhr.status) {
				ajaxEnd();
			}
		}
	});
	return false;
});
$('input[name=postingFrequency]').change(function() {
	var freq = $(this).val();
	if (freq == 'hourly') {
		var quantity = 30;
	}
	if (freq == 'daily') {
		var quantity = 24;
	}
	if (freq == 'weekly') {
		var quantity = 7;
	}
	if (freq == 'monthly') {
		var quantity = 31;
	}
	$('input[name=quantity]').attr("max", quantity).change();
});
$('input[name=quantity]').change(function() {
	var quantity = $(this).val();
	var freq = $('input[name=postingFrequency]:checked').val();
	if (freq == 'hourly') {
		var hour = 1;
	}
	if (freq == 'daily') {
		var hour = 24;
	}
	if (freq == 'weekly') {
		var hour = 7 * 24;
	}
	if (freq == 'monthly') {
		var hour = 31 * 24;
	}
	var rawinterval = Math.round(hour * 60 * 60);
	var interval = Math.round(rawinterval / quantity);
	$('input[name=interval]').val(interval);
	var socialPostLength = $('.social-post');
	var points = new Array(socialPostLength.length);
	for (var i = 0; i < socialPostLength.length; i++) {
		points[i] = i;
	}

	function shuffle(array) {
		var currentIndex = array.length,
			temporaryValue,
			randomIndex;
		while (0 !== currentIndex) {
			randomIndex = Math.floor(Math.random() * currentIndex);
			currentIndex -= 1;
			temporaryValue = array[currentIndex];
			array[currentIndex] = array[randomIndex];
			array[randomIndex] = temporaryValue;
		}
		return array;
	}
	if ($('input[name=shuffle]').is(':checked')) {
		var checkedshuule = 'on';
	} else {
		var checkedshuule = 'off';
	}
	var soreting = $('input[name=sorting]').val();
	if (checkedshuule == 'on') {
		if (soreting == 1) {
			var fianlarr = points;
		} else {
			var fianlarr = shuffle(points);
		}
	} else {
		var fianlarr = points;
	}
	console.log(checkedshuule + ' ' + fianlarr);
	var s_year = $('.table.start select[name=year]').val();
	var s_month = $('.table.start select[name=month]').val();
	var s_day = $('.table.start select[name=day]').val();
	var s_hours = $('.table.start select[name=hour]').val();
	var start = new Date(s_year, s_month, s_day, s_hours).getTime();
	var e_year = $('.table.end select[name=year]').val();
	var e_month = $('.table.end select[name=month]').val();
	var e_day = $('.table.end select[name=day]').val();
	var e_hours = $('.table.end select[name=hour]').val();
	var end = new Date(e_year, e_month, e_day, e_hours).getTime();
	$('input[name=start_time]').val(start);
	$('input[name=end_time]').val(end);
	for (i = 0; i < socialPostLength.length; i++) {
		var index = Number.parseInt([i]);
		var schTime = Number.parseInt(start / 1000) + Number.parseInt(index * interval);
		if (schTime) {
			var newk = fianlarr[i];
			$(socialPostLength[newk]).attr('data-sorttime', schTime);
			$(socialPostLength[newk]).find('.sch-time').attr('data-time', schTime);
			$(socialPostLength[newk]).find('.dev-time').attr('data-time', schTime);
			$(socialPostLength[newk]).find('input[name=sch-time]').val(schTime).change();
			$(socialPostLength[newk]).find('.dev-time').text(timeConverter(schTime));
		} else {
			$(socialPostLength[newk]).find('.sch-time').attr('data-time', null);
			$(socialPostLength[newk]).find('.dev-time').attr('data-time', null);
			$(socialPostLength[newk]).find('input[name=sch-time]').val(0).change();
			$(socialPostLength[newk]).find('.dev-time').text('Schedule not set');
		}
	}
});
var startDate = $('input[name=start]');
var endDate = $('input[name=end]');
if (!startDate.is(':checked')) {
	$('.table.start').find('select').attr('disabled', 'disabled');
	$('[name=end]').attr('disabled', 'disabled');
	$('.table.end').find('select').attr('disabled', 'disabled');
}
startDate.change(function() {
	if (!startDate.is(':checked')) {
		$('.table.start').find('select').attr('disabled', 'disabled');
		$('[name=end]').attr('disabled', 'disabled');
		$('.table.end').find('select').attr('disabled', 'disabled');
	} else {
		$('.table.start').find('select').removeAttr('disabled');
		$('[name=end]').removeAttr('disabled', 'disabled');
	}
});
if (!endDate.is(':checked')) {
	$('.table.end').find('select').attr('disabled', 'disabled');
}
endDate.change(function() {
	if (!endDate.is(':checked')) {
		$('.table.end').find('select').attr('disabled', 'disabled');
	} else {
		$('.table.end').find('select').removeAttr('disabled');
	}
});
$('.start select, .end select').each(function() {
	$(this).change(function() {
		$('input[name=quantity]').change();
	});
});
var $schu = $('#schedule-update');
$schu.find('button[type=submit]').click(function(event) {
	$('input[name=quantity]').change();
	ajaxStart();
	var data = objectifyForm($schu.serializeArray());
	var ids = [];
	var times = [];
	$('span.sch-time').each(function() {
		var postid = $(this).data('id');
		var time = $(this).parents('p').find('input[name=sch-time]').val();
		ids.push(postid);
		times.push(time);
	});
	data.ids = ids;
	data.times = times;
	console.log(data);
	$.ajax({
		type: "POST",
		url: '/schedule-update',
		data: data,
		success: function success(msg) {
			if ($('input[name=shuffle]').is(':checked')) {
				var $wrapper = $('.post_list');
				$wrapper.find('.social-post').sort(function(a, b) {
					return +a.dataset.sorttime - +b.dataset.sorttime;
				}).appendTo($wrapper);
				console.log("by time");
			} else {
				var $wrapper = $('.post_list');
				$wrapper.find('.social-post').sort(function(a, b) {
					return +a.dataset.srorid - +b.dataset.srorid;
				}).appendTo($wrapper);
				console.log("by id");
			}
			ajaxEnd();
		},
		error: function error(xhr, ajaxOptions, thrownError) {
			if (xhr.status) {
				ajaxEnd();
			}
		}
	});
	return false;
});
$('.social-post').each(function() {
	var timedata = $(this).find('.dev-time').data('time');
	console.log(timedata);
	$(this).attr('data-sorttime', timedata);
});
if ($('input[name=shuffle]').is(':checked')) {
	var $wrapper = $('.post_list');
	$wrapper.find('.social-post').sort(function(a, b) {
		return +a.dataset.sorttime - +b.dataset.sorttime;
	}).appendTo($wrapper);
	console.log("by time");
} else {
	$('input[name=quantity]').change();
	var $wrapper = $('.post_list');
	$wrapper.find('.social-post').sort(function(a, b) {
		return +a.dataset.srorid - +b.dataset.srorid;
	}).appendTo($wrapper);
	console.log("by id");
}
$('.social-accounts input').each(function() {
	$(this).change(function() {
		$(this).parents('form').find('button[type=submit]').click();
	});
});
var $tsa = $('#target-social-accounts');
$tsa.find('button[type=submit]').click(function(event) {
	ajaxStart();
	var data = objectifyForm($tsa.serializeArray());
	$.ajax({
		type: "POST",
		url: '/target-social-accounts',
		data: data,
		success: function success(msg) {
			ajaxEnd();
		},
		error: function error(xhr, ajaxOptions, thrownError) {
			if (xhr.status) {
				ajaxEnd();
			}
		}
	});
	return false;
});
var $cgs = $('#change-group-status');

$('input[name=activate]').change(function() {
	if(TempUser.tempUser===true){

	} else {
		$cgs.submit();
	}
});

$cgs.submit(function() {
	//ajaxStart();
	var schi = $('.list-group .social-post input[name=sch-time]').val();
	var data = objectifyForm($cgs.serializeArray());
	data.schi = schi;
	if (data.schi == 0) {
		$('input[name=start]').prop('checked', true).change();
		var MM = ["00", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11"];
		$('input[name=start]').prop('checked', true).change();
		var date = new Date();
		var month = MM[date.getMonth()];
		var day = date.getDate();
		var year = date.getFullYear();
		var hour = date.getHours();
		$('.start select.month option[value=' + month + ']').attr('selected', 'selected').change();
		$('.start select.day option[value=' + day + ']').attr('selected', 'selected').change();
		$('.start select.year option[value=' + year + ']').attr('selected', 'selected').change();
		$('.start select.hour option[value=' + hour + ']').attr('selected', 'selected').change();
		$('input[name=quantity]').change();
		$('#schedule-update button[type=submit]').click();
	}
	$.ajax({
		type: "POST",
		url: '/change-group-status',
		data: data,
		success: function success(msg) {
			if (msg == 'rss-automation') {
				setTimeout(function() {
					window.location.replace(window.Laravel.APP_URL + '/' + msg + '/active/' + data.group_id);
				}, 500);
			} else {
				setTimeout(function() {
					window.location.replace(window.Laravel.APP_URL + '/content-' + msg + '/active/' + data.group_id);
				}, 500);
			}
			ajaxEnd();
		},
		error: function error(xhr, ajaxOptions, thrownError) {
			if (xhr.status) {
				ajaxEnd();
			}
		}
	});
	return false;
});
var $rgu = $('#recycle-group-update');
$('input[name=recycle]').change(function() {
	$rgu.submit();
});
$rgu.submit(function() {
	ajaxStart();
	var data = objectifyForm($rgu.serializeArray());
	$.ajax({
		type: "POST",
		url: '/recycle-group-update',
		data: data,
		success: function success(msg) {
			ajaxEnd();
		},
		error: function error(xhr, ajaxOptions, thrownError) {
			if (xhr.status) {
				ajaxEnd();
			}
		}
	});
	return false;
});
var $sgu = $('#shuffle-group-update');
$('input[name=shuffle]').change(function() {
	$sgu.submit();
});
$sgu.submit(function() {
	ajaxStart();
	var data = objectifyForm($sgu.serializeArray());
	$.ajax({
		type: "POST",
		url: '/shuffle-group-update',
		data: data,
		success: function success(msg) {
			var soreting = $('input[name=sorting]').val();
			if (soreting == 1) {} else {
				$('input[name=quantity]').change();
				$('#schedule-update button[type=submit]').click();
			}
			ajaxEnd();
		},
		error: function error(xhr, ajaxOptions, thrownError) {
			if (xhr.status) {
				ajaxEnd();
			}
		}
	});
	return false;
});
var $sgusf = $('#shuffle-group-update');
var datasf = objectifyForm($sgusf.serializeArray());
if (!datasf.shuffle) {
	console.log(datasf.shuffle);
	$('input[name=quantity]').change();
}
var $hu = $('#hashtags-update');
$hu.find('button[type=submit]').click(function(event) {
	ajaxStart();
	var data = objectifyForm($hu.serializeArray());
	$.ajax({
		type: "POST",
		url: '/hashtags-update',
		data: data,
		success: function success(msg) {
			ajaxEnd();
		},
		error: function error(xhr, ajaxOptions, thrownError) {
			if (xhr.status) {
				ajaxEnd();
			}
		}
	});
	return false;
});
var $dg = $('.group-delete');
$dg.each(function() {
	$(this).find('button[type=submit]').click(function(event) {
		ajaxStart();
		var data = objectifyForm($(this).parents('form').serializeArray());
		var ele = $(this).parents('.group-single');
		$.ajax({
			type: "POST",
			url: '/group-delete',
			data: data,
			success: function success(msg) {
				ele.remove();
				ajaxEnd();
			},
			error: function error(xhr, ajaxOptions, thrownError) {
				if (xhr.status) {
					ajaxEnd();
				}
			}
		});
		return false;
	});
});
var $gnu = $('#group-name-update');
$gnu.find('button[type=submit]').click(function(event) {
	ajaxStart();
	var data = objectifyForm($gnu.serializeArray());
	$.ajax({
		type: "POST",
		url: '/group-name-update',
		data: data,
		success: function success(msg) {
			$('.group-name-update').text(msg);
			ajaxEnd();
		},
		error: function error(xhr, ajaxOptions, thrownError) {
			if (xhr.status) {
				ajaxEnd();
			}
		}
	});
	return false;
});
var $dpbr = $('.delete-post-by-rsslink');
$dpbr.each(function() {
	$(this).find('button[type=submit]').click(function(event) {
		ajaxStart();
		var data = objectifyForm($(this).parents('form').serializeArray());
		var ele = $(this).parents('tr');
		$.ajax({
			type: "POST",
			url: '/delete-post-by-rsslink',
			data: data,
			success: function success(msg) {
				ele.remove();
				ajaxEnd();
				location.reload();
			},
			error: function error(xhr, ajaxOptions, thrownError) {
				if (xhr.status) {
					ajaxEnd();
				}
			}
		});
		return false;
	});
});
var $PostUpdate = $('.post-update');
$PostUpdate.each(function() {
	var base = $(this);
	$(this).find('button[type=submit]').click(function(event) {
		ajaxStart();
		var data = objectifyForm($(this).parents('form').serializeArray());
		$.ajax({
			type: "POST",
			url: '/post-update',
			data: data,
			success: function success(msg) {
				console.log(data);
				if (data.text) {
					base.parents('.social-post').find('h4').text(data.text);
				}
				if (data.rsslink) {
					base.parents('.social-post').find('h4').text(data.rsslink);
				}
				base.parents('.social-post').find('.urlup').text(data.link);
				base.parents('.social-post').find('.imageup').text(data.image);
				if (data.fb) {
					base.parents('.social-post').find('.fac > div').text(data.fb);
				}
				if (data.g) {
					base.parents('.social-post').find('.goo > div').text(data.g);
				}
				if (data.in) {
					base.parents('.social-post').find('.lin > div').text(data.in);
				}
				if (data.tw) {
					base.parents('.social-post').find('.twit > div').text(data.tw);
				}
				ajaxEnd();
			},
			error: function error(xhr, ajaxOptions, thrownError) {
				if (xhr.status) {
					ajaxEnd();
				}
			}
		});
		return false;
	});
	$(this).find('button[type=button].send_post_trash').click(function(event) {
		ajaxStart();
		var data = objectifyForm($(this).parents('form').serializeArray());
		var ele = $(this).parents('.list-group-item');
		$.ajax({
			type: "POST",
			url: '/post-delete',
			data: data,
			success: function success(msg) {
				ele.remove();
				ajaxEnd();
			},
			error: function error(xhr, ajaxOptions, thrownError) {
				if (xhr.status) {
					ajaxEnd();
				}
			}
		});
		return false;
	});
});
$('.curation-refresh').each(function() {
	var $curb = $(this);
	$curb.find('button[type=submit]').click(function(event) {
		ajaxStart();
		var data = objectifyForm($curb.serializeArray());
		$.ajax({
			type: "POST",
			url: '/curation-refresh',
			data: data,
			success: function success(msg) {
				console.log(msg);
				ajaxEnd();
			},
			error: function error(xhr, ajaxOptions, thrownError) {
				if (xhr.status) {
					ajaxEnd();
				}
			}
		});
		return false;
	});
});
$(function() {
	$(".group-items").sortable({
		connectWith: ".group-items",
		placeholder: 'ui-state-highlight',
		receive: function receive(event, ui) {
			var group_id = ui.item.data('id');
			var current_status = ui.item.data('status');
			var target_status = $(this).data('status');
			var data = {
				'group_id': group_id,
				'current_status': current_status,
				'target_status': target_status
			};
			if (current_status != target_status) {
				ajaxStart();
				$.ajax({
					type: "POST",
					url: '/drag-drop',
					data: data,
					headers: {
						'X-CSRF-TOKEN': window.Laravel.csrfToken
					},
					success: function success(msg) {
						var oldhtml = ui.item[0].outerHTML;
						ui.item.html('');
						var newhtml = oldhtml.replace(RegExp(current_status, 'g'), target_status);
						if (current_status == 'pending') {}
						ui.item.replaceWith(newhtml);
						ajaxEnd();
					},
					error: function error(xhr, ajaxOptions, thrownError) {
						if (xhr.status) {
							ajaxEnd();
						}
					}
				});
			}
		}
	}).disableSelection();
});
$('.social-account-id-group').each(function() {
	var $base = $(this);
	$base.find('button[type=submit]').click(function(event) {
		var form = $(this).parent('form');
		var data = form.serializeArray();
		$.ajax({
			type: "POST",
			url: '/social-account-id-group',
			data: data,
			success: function success(msg) {
				console.log(msg);
				ajaxEnd();
			},
			error: function error(xhr, ajaxOptions, thrownError) {
				if (xhr.status) {
					ajaxEnd();
				}
			}
		});
		return false;
	});
});
$('input[name=active_inactive]').each(function() {
	$(this).change(function() {
		$(this).parent().submit();
	});
});
$('.active-deactive-account').each(function() {
	var $base = $(this);
	$base.submit(function() {
		var data = $base.serializeArray();
		$.ajax({
			type: "POST",
			url: '/account-active-inactive',
			data: data,
			success: function success(msg) {
				console.log(msg);
				ajaxEnd();
			},
			error: function error(xhr, ajaxOptions, thrownError) {
				if (xhr.status) {
					ajaxEnd();
				}
			}
		});
		return false;
	});
});
var $rag = $('#re-active-group');
$rag.find('button[type=submit]').click(function(event) {
	ajaxStart();
	var data = objectifyForm($rag.serializeArray());
	$.ajax({
		type: "POST",
		url: '/re-activate',
		data: data,
		success: function success(msg) {
			console.log(msg);
			if (msg == 'rss-automation') {
				setTimeout(function() {
					window.location.replace(window.Laravel.APP_URL + '/' + msg + '/active/' + data.group_id);
				}, 500);
			} else {
				setTimeout(function() {
					window.location.replace(window.Laravel.APP_URL + '/content-' + msg + '/active/' + data.group_id);
				}, 500);
			}
			ajaxEnd();
		},
		error: function error(xhr, ajaxOptions, thrownError) {
			if (xhr.status) {
				ajaxEnd();
			}
		}
	});
	return false;
});
//////////////////////////////
var ctx = document.getElementById("myChart");
if (ctx != null) {
	var myDoughnutChart = new Chart(ctx, {
		type: 'doughnut',
		data: data,
		options: {
			legend: !1,
			responsive: !1
		}
	});
	var total = 0;
	for (var i = 0; i < data.datasets[0].data.length; i++) {
		total += data.datasets[0].data[i] << 0;
	}
	$('.channel-activity .media-left .total-post').html(numberWithCommas(total) + ' <br><span>Posts</span>');
}

function numberWithCommas(x) {
	return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
var ctx2 = document.getElementById("myChart2");
if (ctx2 != null) {
	var myLineChart = new Chart(ctx2, {
		type: 'line',
		data: data2,
		options: {
			scales: {
				yAxes: [{
					stacked: true
				}]
			}
		}
	});
}
var $sendidea = $('#send-idea');
$sendidea.find('button').click(function(event) {
	ajaxStart();
	var data = objectifyForm($sendidea.serializeArray());
	console.log
	$.ajax({
		type: "POST",
		url: '/send-idea',
		data: data,
		success: function success(msg) {
			$('.feedback_from').prepend('<div class="alert alert-success" role="alert"><strong>Thanks for your feedback, it is appreciated. I’ll be sure to follow up if I need additional info or have questions</strong></div>');
			console.log(msg);
			ajaxEnd();
		},
		error: function error(xhr, ajaxOptions, thrownError) {
			if (xhr.status) {
				ajaxEnd();
			}
		}
	});
	return false;
});
$('.close-account').click(function() {
	var r = confirm("Data will be deleted at the end of your billing cycle.");
	if (r == true) {
		$('#close-account-from').submit();
	} else {
		console.log("ok");
	}
});
var widget = AppcuesWidget(Appcues.user());
widget.init("#my-widget", {
	position: "left",
});
$(window).on('load', function() {
	var window_height = $(window).height();
	$('.group-col .group-items').each(function() {
		var top = $(this).offset().top;
		$(this).height(window_height - top);
	})
	$('.app-body').each(function() {
		var top = $(this).offset().top + 140;
		$(this).height(window_height - top);
	})
	$('.dropdown-pop.post-to').each(function() {
		var top = $(this).parent().offset().top + 336;
		var need = window_height - top;
		console.log(need);
		if (need < 0) {
			$(this).css({
				'top': need + 'px'
			})
		}
	});
	$(".post_list").sortable({
		start: function(event, ui) {
			$('input[name=sorting]').val('1').change();
		},
		stop: function(event, ui) {
			var moved = ui.item,
				replaced = ui.item.prev();
			if (replaced.length == 0) {
				replaced = ui.item.next();
			}
			if ($('input[name=shuffle]').is(':checked')) {} else {
				$('input[name=shuffle]').prop('checked', true).change();
			}
			$('#schedule-update button[type=submit]').click();
			setTimeout(function() {
				$('input[name=sorting]').val('').change();
			}, 1000);
		}
	});
});
$(window).on('load', function() {
	setTimeout(function() {
		$('.list-group.post_list').css({
			'opacity': '1',
		});
	}, 100);
})
$('.dLabelbutton').each(function() {
	var vas = $(this);
	vas.click(function() {
		$('.media-right.media-middle').removeClass('upit');
		$(this).parents('.media-right.media-middle').addClass('upit');
	})
});
if ($(window).width() < 768) {
	$('.dropdown .dropdown-menu.dropdown-pop').css({
		'min-width': 'initial',
		'width': $(window).width() - 50 + 'px',
	})
}

function objectifyForm(formArray) {
	var returnArray = {};
	for (var i = 0; i < formArray.length; i++) {
		returnArray[formArray[i]['name']] = formArray[i]['value'];
	}
	return returnArray;
}

$('.post-update').find('button[type=button].send_now_post').click(function(event) {
	ajaxStartt();
	var data = objectifyForm($(this).parents('form').serializeArray());
	var ele = $(this).parents('.list-group-item');
	$.ajax({
		type: "POST",
		url: '/post-sent-now',
		data: data,
		success: function success(msg) {
			ajaxEndd();
			//location.reload();
		},
		error: function error(xhr, ajaxOptions, thrownError) {
			if (xhr.status) {}
		}
	});
	return false;
});

function ajaxStartt() {
	$('.ajax-working').fadeIn();
}

function ajaxEndd() {
	$('.ajax-working').fadeOut();
}

$('#repeat_wait_tog').change(function() {
	if ($(this).is(':checked')) {
		$('input[name=repeat_wait]').val(1).change();
	} else {
		$('input[name=repeat_wait]').val(0).change();
	}
});

$('input[name=repeat_wait]').change(function() {
	$('.quanwait_date').text($(this).val());
	ajaxStartt();
	var data = objectifyForm($('#recycle-group-update').serializeArray());
	console.log(data);
	$.ajax({
		type: "POST",
		url: '/wait-date',
		data: data,
		success: function success(msg) {
			ajaxEndd();
		},
		error: function error(xhr, ajaxOptions, thrownError) {
			if (xhr.status) {
				ajaxEndd();
			}
		}
	});
});

var rngquanwait_date = document.querySelector("input[name=repeat_wait]");
if (rngquanwait_date) {
	var read = function read(evtType) {
		rngquanwait_date.addEventListener(evtType, function() {
			window.requestAnimationFrame(function() {
				$('.quanwait_date').text(rngquanwait_date.value);
			});
		});
	};
	read("mousedown");
	read("mousemove");
}

$('.import_from_buffer').click(function() {
	ajaxStartt();
	console.log('clicked');
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	$.ajax({
		type: "GET",
		url: '/import-from-buffer',
		data: {},
		success: function success(msg) {
			ajaxEndd();
			window.location.reload();
		},
		error: function error(xhr, ajaxOptions, thrownError) {
			if (xhr.status) {
				ajaxEndd();
				window.location.reload();
			}
		}
	});
	return false;
});


var rebrandly_key = $('#rebrandly_key-form');

rebrandly_key.find('button').click(function(){
	ajaxStart();
	var data = objectifyForm(rebrandly_key.serializeArray());
	$.ajax({
		type: "POST",
		url: '/rebrandly_key',
		data: data,
		success: function success(msg) {
			ajaxEnd();
		},
		error: function error(xhr, ajaxOptions, thrownError) {
			if (xhr.status) {
				ajaxEnd();
			}
		}
	});

})
