<!DOCTYPE html>


<html>

<form action="{{ route('upload') }}" method="post" enctype="multipart/form-data">
    @csrf
    <button type="submit">Upload</button>
</form>




</html>
