document.addEventListener("DOMContentLoaded",(function(){var e=document.getElementById(adminNoticeData.notice_slug);e&&e.querySelector(".notice-dismiss").addEventListener("click",(function(){var e=new XMLHttpRequest;e.open("POST",adminNoticeData.ajax_url,!0),e.setRequestHeader("Content-Type","application/x-www-form-urlencoded"),e.onreadystatechange=function(){4===e.readyState&&200===e.status&&!0===JSON.parse(e.responseText).success&&(document.getElementById(adminNoticeData.notice_slug).style.display="none")},e.send("action=dismiss_admin_notice&notice="+encodeURIComponent(adminNoticeData.notice_slug)+"&security="+encodeURIComponent(adminNoticeData.security))}))}));