<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý khách sạn</title>
</head>
<body>
    <h1>Danh sách phòng</h1>
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Tên phòng</th>
            <th>Loại phòng</th>
            <th>Giá</th>
            <th>Trạng thái</th>
        </tr>

        <?php foreach ($rooms as $room): ?>
            <tr>
                <td><?= $room['id'] ?></td>
                <td><?= $room['name'] ?></td>
                <td><?= $room['type'] ?></td>
                <td><?= number_format($room['price'], 0, ',', '.') ?> VNĐ</td>
                <td><?= $room['status'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>