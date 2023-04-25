<div class="col-sm-8 mt-3">
  <form id="userForm" method="post" enctype="multipart/form-data">
    <div class="form-group row">
      <label for="email" class="col-sm-2 col-form-label">Chage Email</label>
      <div class="col-sm-8">
        <input type="email" class="form-control" id="email" placeholder="Email" name="email">
      </div>
    </div>
    <div class="form-group" id="photoDiv">
      <div class="row row-col">
        <div class="col-2">
          <label class="form-label">Profile Picture</label>
        </div>
        <div class="col-8">
          <input type="file" class="form-control" id="profilePhoto" name="profilePhoto" />
        </div>
      </div>
    </div>
    <div class="form-group row">
      <div class="col-2">
        <label class="form-label">Default Brand</label>
      </div>
      <div class="col-8">
        <select class="form-select" aria-label="Default select example" name="brand">
          <?php foreach ($brands as $brand) : ?>
            <option value="<?= $brand["name"] ?>"><?= $brand["name"] ?></option>
          <?php endforeach ?>
        </select>
      </div>
    </div>
    <div class="form-group row">
      <div class="col-sm-2">
        <input type="submit" class="form-control btn btn-primary" value="Save">
      </div>
    </div>
  </form>
</div>