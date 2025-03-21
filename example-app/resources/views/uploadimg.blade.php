<h2>IMG</h2>
 <form action="/readimg" enctype="multipart/form-data" method="post">
          @csrf
        <input type="file" name="image" placeholder="select multiple image">
        <button type="submit">Save</button>
    </form>