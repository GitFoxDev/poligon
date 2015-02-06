$(document).ready(function() {
	var dropZone = $('#dropZone'),
		resultZone = $('#resultZone')
		maxFileSize = 2*1024*1024;
	
	if (typeof(window.FileReader) == 'undefined') {
		dropZone.text('Не поддерживается браузером!');
		dropZone.addClass('error');
	}
	
	dropZone[0].ondragover = function() {
		dropZone.addClass('hover');
		return false;
	};

	dropZone[0].ondragleave = function() {
		dropZone.removeClass('hover');
		return false;
	};
	
	dropZone[0].ondrop = function(event) {
		event.preventDefault();
		dropZone.removeClass('hover');
		//dropZone.addClass('drop');
		
		var file = event.dataTransfer.files[0];

		if (file.size > maxFileSize) {
			resultZone.text('Файл слишком большой!');
			resultZone.addClass('error');
			return false;
		}
		
		var xhr = new XMLHttpRequest();
        xhr.upload.addEventListener('progress', uploadProgress, false);
        xhr.onreadystatechange = stateChange;
        xhr.open('POST', 'index.php');
        xhr.setRequestHeader('X-FILE-NAME', file.name);
		var formData = new FormData();
		formData.append("file", file);
		formData.append("upload", "upload");
        xhr.send(formData);
	};
	
	function uploadProgress(event) {
		var percent = parseInt(event.loaded / event.total * 100);
		resultZone.text('Загрузка: ' + percent + '%');
		resultZone.addClass('upload');
    }
	
	function stateChange(event) {
		resultZone.removeClass();
		if (event.target.readyState == 4) {
			if (event.target.status == 200) {
				str_arr = event.target.responseText.split(";");
				if (str_arr[0] == '1') {
					resultZone.addClass('success');
					getHistory();
				} else {
					resultZone.addClass('error'); }
				resultZone.text(str_arr[1]);
			} else {
				resultZone.text('Произошла ошибка!');
				resultZone.addClass('error');
			}
		}
    }
	
	var fileContainer = $('#file-input');
	fileContainer.change(function() {
      upload(this.files);
      $('form').get(0).reset();
    })
});

function getHistory() {
	$.ajax ({
		type: "POST", url: 'index.php',
		data: "act=history", dataType : "text",
		success: function (data) {
			$('#historyZone').html(data);
		}
	});
}
getHistory();

function getBody(id) {
	$.ajax ({
		type: "POST", url: 'index.php',
		data: "act=body&id="+id, dataType : "text",
		success: function (data) {
			$('#bodyZone').html(data);
			$('#bodyZone').addClass('result')
		}
	});
}