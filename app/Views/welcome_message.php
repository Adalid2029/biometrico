<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> -->
    <link rel="stylesheet" href="plugins/bootstrap/bootstrap.min.css">
    <title>Hello, world!</title>
</head>

<body>
    <style>
        .autocomplete-suggestions {
            border: 1px solid #999;
            background: #FFF;
            overflow: auto;
        }

        .autocomplete-suggestion {
            padding: 2px 5px;
            white-space: nowrap;
            overflow: hidden;
        }

        .autocomplete-selected {
            background: #F0F0F0;
        }

        .autocomplete-suggestions strong {
            font-weight: normal;
            color: #3399FF;
        }

        .autocomplete-group {
            padding: 2px 5px;
        }

        .autocomplete-group strong {
            display: block;
            border-bottom: 1px solid #000;
        }
    </style>
    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
        <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
        </symbol>
        <symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
        </symbol>
        <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
        </symbol>
    </svg>
    <!-- content -->
    <div class="container">
        <form id="send-person-marking">
            <input type="hidden" name="ci">
            <h1>Hello, world!</h1>
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="floatingInput" placeholder="Nombre, Apellido, CI" required>
                <label for="floatingInput">Nombre, Apellido, CI</label>
            </div>
            <div class="form-floating mb-3">
                <input type="time" class="form-control" id="floatingPassword" placeholder="Hora" name="hour" required>
                <label for="floatingPassword">Hora</label>
            </div>
            <div class="form-floating">
                <input type="date" class="form-control" id="date" placeholder="Fecha" name="date-marking" required>
                <label for="date">Fecha</label>
            </div>
            <hr>
            <div class="col-12">
                <button class="btn btn-primary" type="submit">Enviar</button>
            </div>
            <div class="alert alert-success d-flex align-items-center d-none" role="alert">
                <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:">
                    <use xlink:href="#check-circle-fill" />
                </svg>
                <div>
                    Correcto
                </div>
            </div>

            <div class="alert alert-danger d-flex align-items-center d-none" role="alert">
                <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:">
                    <use xlink:href="#exclamation-triangle-fill" />
                </svg>
                <div>
                    Incorrecto
                </div>
            </div>
        </form>
        <table class="table table-striped">
            <thead>
                <th>nro</th>
                <th>ci</th>
                <th>nombre</th>
                <th>marcado</th>
            </thead>
            <tbody>
                <?php foreach ($personMarking as $key => $value) : ?>
                    <tr>
                        <td><?= $value['id_persona_marcado'] ?></td>
                        <td><?= $value['ci'] ?></td>
                        <td><?= $value['nombre'] ?> <?= $value['paterno'] ?> <?= $value['materno'] ?></td>
                        <td><?= $value['marcado'] ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>




    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script> -->
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <script src="plugins/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="plugins/jquery/jquery-3.6.0.min.js"></script>
    <script src="plugins/jQuery-Autocomplete-master/dist/jquery.autocomplete.min.js"></script>
    <script>
        $(document).ready(function() {
            $('[name=date-marking]').val(localStorage.getItem('dateMarking'));
            $('#floatingInput').autocomplete({
                serviceUrl: 'search/person',
                minChars: 5,
                onSelect: function(suggestion) {
                    $('[name=ci]').val(suggestion.data);
                },
                onSearchComplete: function(query, suggestions) {
                    if (suggestions.length === 0) {
                        $('[name=ci]').val('');
                    }
                },
                onInvalidateSelection: function() {
                    $('[name=ci]').val('');
                }
            });
            $('#floatingInput').keyup(function(e) {
                if (e.keyCode == 8) {
                    $('[name=ci]').val('');
                }
            });
            $('[name=date-marking]').on('change', function() {
                localStorage.setItem('dateMarking', $('[name=date-marking]').val());
            })
            $('#send-person-marking').on('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();

                var form = $(this);
                var data = form.serialize();
                $.post('savePersonMaking', data, function(response) {
                    location.reload();
                }, 'json').fail(function(xhr, status, error) {
                    alert(xhr.responseText);
                });
            })
        });
    </script>
    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
</body>

</html>