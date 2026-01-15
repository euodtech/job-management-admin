<script type="text/javascript">
	function sweatAlertLoader() {
        $('#content-blur').addClass('blur');
        let timerInterval;
        Swal.fire({
            toast: true,
            showConfirmButton: false,
            title: "Wait Just a Moment!",
            html: '<b></b>', // Include an empty <b> element
            timerProgressBar: true,
            customClass: {
                container: "custom-swal-container",
                popup: "custom-swal-popup",
                loader: "custom-swal-loader",
            },
            didOpen: (toast) => {
                Swal.showLoading(); // Show the loading animation
                const timer = Swal.getHtmlContainer().querySelector("b");
                timerInterval = setInterval(() => {
                    if (timer) {
                        timer.textContent = Swal.getTimerLeft();
                    }
                }, 100);
            },
            willClose: () => {
                $('#content-blur').toggleClass('blur');
                clearInterval(timerInterval);
            }
        });
    }

   function refreshTable(callback) {
        var table = $('#example1').DataTable();
        table.destroy(); 

        $('.ref').load(location.href + ' .this', function() {
            setTimeout(function() { 
                $('#example1').DataTable({
                    "responsive": false,
                    "autoWidth": true,
                    "stateSave": true,
                    "paging": true
                });

                if (typeof callback === 'function') {
                    callback(); 
                }
            }, 100);
        });

        $('.ref3').load(location.href + ' .this3', function() {
            setTimeout(function() { 
                $("#example3").DataTable({
                    "stateSave": true,
                    "paging": true,
                    "scrollX": true

                });
                if (typeof callback === 'function') {
                    callback(); 
                }
            }, 100);
        });
    }


    function afterTableRefresh() {
        Swal.close(); 
    }

    function import_file(form) {
        $('#content-blur').addClass('blur');
        sweatAlertLoader(); 
        var formData = new FormData(form);
        $.ajax({
            url: $(form).attr('action'), 
            type: $(form).attr('method'),
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                refreshTable(afterTableRefresh);
                form.reset();
            },
            error: function(xhr, status, error) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Import Failed',
                    text: 'An error occurred while importing the file.'
                });
            }
        });
    }

    function create(url,serializedData)
    {
        $('#content-blur').addClass('blur');
        $('#modal-add').modal('hide');

        sweatAlertLoader(); 

        $.ajax({
            url: url,
            type: 'POST',
            data: serializedData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    refreshTable(afterTableRefresh);
                   
                } else {
                    alert('Terjadi kesalahan: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Terjadi kesalahan: ' + status);
            }
        });
    }

    function update(urlUpdate,serializedData)
    {

        $('#content-blur').addClass('blur');
        $('#modal-add').modal('hide');

        sweatAlertLoader(); 
        $.ajax({
            url: urlUpdate,
            type: 'POST',
            data: serializedData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    refreshTable(afterTableRefresh);
                } else {
                    alert('Terjadi kesalahan: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Terjadi kesalahan: ' + error);
            }
        });
    }

    function deleteItem(url)
    {
        $('#content-blur').addClass('blur');
        sweatAlertLoader(); 
    	$.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
          		    refreshTable(afterTableRefresh);
                } else {
                    alert('Terjadi kesalahan: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Terjadi kesalahan: ' + error);
            }
        });
    }
    function deleteProduct(url)
    {
        $('#content-blur').addClass('blur');
        sweatAlertLoader(); 
    	$.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.close();
                    location.reload();
                } else {
                    alert('Terjadi kesalahan: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Terjadi kesalahan: ' + error);
            }
        });
    }

	function updateImage(urlUpdate,serializedData)
	{
        $('#content-blur').addClass('blur');
        $('#modal-add').modal('hide');
        sweatAlertLoader();

		$.ajax({
            url: urlUpdate,
            type: 'POST',
            data: serializedData,
            dataType: 'json',
            processData: false,  
            contentType: false,
            success: function(response) {
                if (response.status === 'success') {
          		    refreshTable(afterTableRefresh);
                } else {
                    alert('Terjadi kesalahan: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Terjadi kesalahan: ' + error);
            }
        });
	}

	function createImage(url,serializedData)
	{

        $('#content-blur').addClass('blur');
        $('#modal-add').modal('hide');
        sweatAlertLoader();
		
        $.ajax({
            url: url,
            type: 'POST',
            data: serializedData,
            dataType: 'json',
            contentType: 'application/x-www-form-urlencoded',
            processData: false,  
            contentType: false,
            success: function(response) {
                // console.log(response);
                if (response.status === 'success') {
          		    refreshTable(afterTableRefresh);
                } else {
                    alert('Terjadi kesalahan: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Terjadi kesalahan: ' + status);
            }
        });
	}

</script>