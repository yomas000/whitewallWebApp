<?= $this->extend('Navigation') ?>
<?= $this->section('MainPage') ?>

<div class="row">
    <div class="col-sm-4">
        <div class="row m-2">
            <div class="col-sm-8">
                <div id="actions" class="row" style="display: none">
                    <p id="infoSelect" class="mr-2"></p>
                    <a href="#">Delete</a>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-funnel" viewBox="0 0 16 16">
                            <path
                                d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5v-2zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2h-11z" />
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
                    <?php foreach($menuItems as $menuItem) : ?>
                        <tr id="<?= $menuItem["title"] ?>" onclick="">
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
        <div class="card m-2 p-2">
            <form>
                <div class="form-group row">
                    <label for="title" class="col-sm-3 col-form-label">Menu Title</label>
                    <input type="text" class="form-control col-sm-8" id="title" placeholder="Instagram">
                </div>
                
                <div class="row">
                    <div class="col-sm-3">
                        <label for="force-switch" class="col-form-label">External Link</label>
                    </div>
                    <div class="col-sm-8">
                        <div class="row">
                            <div class="custom-control custom-switch mr-3">
                                <input type="checkbox" class="custom-control-input" id="target-switch">
                                <label class="custom-control-label" for="target-switch"></label>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="sequence" class="form-group row">
                    <label for="s-select" class="col-sm-3 col-form-label">Sequence</label>

                    <select id="s-select" class="custom-select col-sm-8">
                        <option selected value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select>
                </div>

                <div class="float-right row">
                    <p id="updated" class="mr-2"></p>
                    <button class="btn btn-primary m-2">Save</button>
                    <button class="btn btn-danger m-2">Remove</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>