<script type="text/javascript">

    // ── Global CSRF token injection for all AJAX requests and forms ──
    (function() {
        var csrfName = $('meta[name="csrf-name"]').attr('content');
        var csrfHash = $('meta[name="csrf-token"]').attr('content');
        if (csrfName && csrfHash) {
            // Inject CSRF into all AJAX POST/PUT/DELETE requests
            $.ajaxSetup({
                beforeSend: function(xhr, settings) {
                    if (settings.type && settings.type.toUpperCase() !== 'GET') {
                        if (typeof settings.data === 'string') {
                            settings.data += '&' + csrfName + '=' + encodeURIComponent(csrfHash);
                        } else if (settings.data instanceof FormData) {
                            settings.data.append(csrfName, csrfHash);
                        } else if (typeof settings.data === 'object' && settings.data !== null) {
                            settings.data[csrfName] = csrfHash;
                        } else {
                            settings.data = csrfName + '=' + encodeURIComponent(csrfHash);
                        }
                    }
                }
            });

            // Inject CSRF hidden field into all POST forms that don't already have it
            $('form[method="post"], form[method="POST"]').each(function() {
                if (!$(this).find('input[name="' + csrfName + '"]').length) {
                    $(this).prepend('<input type="hidden" name="' + csrfName + '" value="' + csrfHash + '">');
                }
            });
        }
    })();

    // ── Global form-validation helpers ──────────────────────────────
    function setFieldError(fieldId, msg) {
        var $field = $('#' + fieldId);
        $field.closest('.form-group').addClass('field-error');
        $('#error-' + fieldId).text(msg);
    }

    function clearFieldError(fieldId) {
        var $field = $('#' + fieldId);
        $field.closest('.form-group').removeClass('field-error');
        $('#error-' + fieldId).text('');
    }

    function clearAllFieldErrors(container) {
        var $c = $(container);
        $c.find('.form-group').removeClass('field-error');
        $c.find('.inline-error').text('');
    }

    // Auto-clear errors when user types / selects
    $(document).on('input', '.form-group input, .form-group textarea', function () {
        var $fg = $(this).closest('.form-group');
        $fg.removeClass('field-error');
        $fg.find('.inline-error').text('');
    });
    $(document).on('change', '.form-group select, .form-group input[type="radio"], .form-group input[type="file"]', function () {
        var $fg = $(this).closest('.form-group');
        $fg.removeClass('field-error');
        $fg.find('.inline-error').text('');
    });

    // Modal helpers (Preline overlay + CSS polyfill guarantee)
    function showModal(selector) {
        const el = document.querySelector(selector);
        if (!el) return;

        // Try Preline's HSOverlay for extras (focus trap, aria, etc.)
        if (typeof HSOverlay !== 'undefined') {
            try { HSOverlay.open(el); } catch (e) { /* CSS polyfill handles it */ }
        }

        // Always ensure the modal is open — classList.add is a no-op if already present
        el.classList.remove('hidden');
        el.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function hideModal(selector) {
        const el = document.querySelector(selector);
        if (!el) return;

        // Try Preline's HSOverlay
        if (typeof HSOverlay !== 'undefined') {
            try { HSOverlay.close(el); } catch (e) { /* CSS polyfill handles it */ }
        }

        // Always ensure the modal is closed
        el.classList.add('hidden');
        el.classList.remove('open');
        document.body.style.overflow = '';
    }

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

                // Re-init Preline overlays after dynamic content load
                if (window.HSStaticMethods) {
                    window.HSStaticMethods.autoInit();
                }

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

                // Re-init Preline overlays after dynamic content load
                if (window.HSStaticMethods) {
                    window.HSStaticMethods.autoInit();
                }

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
        hideModal('#modal-add');

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
        hideModal('#modal-add');

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
        hideModal('#modal-add');
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
        hideModal('#modal-add');
        sweatAlertLoader();

        $.ajax({
            url: url,
            type: 'POST',
            data: serializedData,
            dataType: 'json',
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
