<link href='https://cdn.jsdelivr.net/npm/froala-editor@latest/css/froala_editor.pkgd.min.css' rel='stylesheet' type='text/css' />

<!-- <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet"> -->
<!-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

<div class="row">
    <div class="col-sm-4">
        <div class="row m-2">
            <div class="col-sm-8">
                <div id="actions" class="row" style="display: none">
                    <div class="col-2">
                        <p id="infoSelect" class=""></p>
                    </div>
                    <div class="col-10">
                        <div class="row">
                            <div class="col-1">

                            </div>
                            <div class="col-4 text-center">
                                <?php if ($view[$pageName]["remove"]) : ?>
                                    <button class="btn btn-danger" id="delete">Delete</button>
                                <?php endif ?>
                            </div>
                            <div class="col-1">

                            </div>
                            <div class="col-6">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-funnel" viewBox="0 0 16 16">
                            <path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5v-2zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2h-11z" />
                        </svg>
                        Filters
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                        <button class="dropdown-item" type="button">Action</button>
                        <button class="dropdown-item" type="button">Another action</button>
                        <button class="dropdown-item" type="button">Something else here</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card m-2">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col"><input id="check-all" type="checkbox" class="checkbox-lg"></th>
                        <th scope="col">Title</th>
                        <td>Sequence</td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($menuItems as $menuItem) : ?>
                        <tr id="<?= $menuItem["title"] ?>" onclick="getMenu(this);">
                            <th scope="row"><input type="checkbox" class="checkbox-lg"></th>
                            <td><?= $menuItem["title"] ?></td>
                            <td><?= $menuItem["sequence"] ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-sm-8 mt-3">
        <div class="card m-2 p-2" id="form-div" <?php if (!$view[$pageName]["add"]) echo 'style="display: none;"' ?>>
            <div class="col d-flex justify-content-center">
                <h2 id="data-title" class="mr-4">Add Menu Item</h2>
            </div>
            <form id="menuData">
                <div class="form-group">
                    <label for="title">Menu Title</label>
                    <input type="text" class="form-control" id="title" placeholder="Instagram">
                </div>


                <div class="container row justify-content-center mb-2">
                    <div class="col-6">
                        <div class="form-check mr-4">
                            <input class="form-check-input" type="radio" name="linkRadio" id="linkRadio" checked>
                            <label class="form-check-label" for="linkRadio">Link</label>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="customRadio" id="customRadio">
                            <label class="form-check-label" for="customRadio">Custom Page</label>
                        </div>
                    </div>
                </div>

                <div class="form-group" id="linkDiv">
                    <input type="text" class="form-control" id="link" placeholder="https://instagram.com/username">
                </div>

                <div id="customDiv" class="mb-3" style="display: none;">
                    <div id="summernote"></div>

                </div>

                <div id="sequence" class="form-group">
                    <label for="s-select">Sequence</label>

                    <select id="s-select" class="custom-select">
                        <?php for ($i = 1; $i <= count($menuItems) + 1; $i++) : ?>
                            <option value="<?= $i ?>"><?= $i ?></option>
                        <?php endfor ?>
                    </select>
                </div>
            </form>
            <div class="float-right">
                <p id="updated" class="mr-2"></p>
                <button class="btn btn-primary m-2" onclick="$('#menuData').submit();" id="save">Add New Menu Item</button>
                <button class="btn btn-danger m-2" id="remove" style="display: none;">Remove</button>
            </div>
            <div class="alert alert-success" role="alert" style="display: none;">
                Success
            </div>
            <div class="alert alert-danger" role="alert" style="display: none;">
            </div>
        </div>
    </div>
</div>

<!-- <script type='text/javascript' src='https://cdn.jsdelivr.net/npm/froala-editor@latest/js/froala_editor.pkgd.min.js'></script> -->
<script>
    // new FroalaEditor('#editor', {

    //     imageUploadURL: '/menu/update',

    //     fileUploadParams: {
    //         id: 'my_editor'
    //     }
    // })
    $('#summernote').summernote({
        disableDragAndDrop: true,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link']],
            ['view', ['codeview', 'help']],
        ]
    });
</script>
<script src="/js/menu.js"></script>