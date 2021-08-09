<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <main>
        <form action="<?= base_url('home/upload') ?>" method="post" enctype="multipart/form-data">
            <div>
                <label for="">File excel</label>
                <input type="file" name="excel" id="">
            </div>

            <button type="submit">Kirim</button>
        </form>
    </main>

</body>
</html>