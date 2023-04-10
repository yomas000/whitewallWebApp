<?= $this->extend('Navigation') ?>
<?= $this->section('MainPage') ?>
<div class="row m-3">
    <div class="col-sm-8">
        <div class= "card p-2">
            <div class="row m-2">
                <div class="col-sm-10">
                    <div id="actions" class="row" style="display: none">
                        <p id="infoSelect" class="mr-2"></p>
                        <a href="#">Delete</a>
                        <p class="mr-2 ml-2"> | </p>
                        <a href="#">Change User</a>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-funnel" viewBox="0 0 16 16">
                                <path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5v-2zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2h-11z"/>
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
            <table class="table table-hover">
                <thead>
                    <tr>
                    <th scope="col"><input id="check-all" type="checkbox" class="checkbox-lg"></th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($images)) : ?>
                        <?php foreach ($images as $image) : ?>
                            <tr id="<?= $image["name"] ?>" onclick="getImg(this);">
                                <th scope="row"><input type="checkbox" class="checkbox-lg"></th>
                                <td class="w-25"><image class="img-sm rounded" src="<?= $image["path"] ?>"></td>
                                <td><?= $image["name"] ?></td>
                                <td><?= $image["collection"] ?></td>
                                <td><a href="#"><?= $image["category"] ?></a></td>
                            </tr>
                        <?php endforeach ?>
                    <?php endif ?>
                    <tr id="1" onclick="">
                        <th scope="row"><input type="checkbox" class="checkbox-lg"></th>
                        <td>Name</td>
                        <td>example@example.com</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card p-2">
            
        </div>
    </div>
</div>
<?= $this->endSection() ?>