<?php
include '../other-functions/db_connect.php';
include '../topnav/top.php';

$searchTerm = '';
$whereClause = '';

if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $searchTerm = $conn->real_escape_string(trim($_GET['search']));
    $whereClause = "WHERE brand_name LIKE '%$searchTerm%' OR product_name LIKE '%$searchTerm%'";
}

$query = "SELECT * FROM products $whereClause ORDER BY id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Search Results</title>
    <link rel="stylesheet" href="../other-functions/bootstrap.min.css" />
    <style>
        /* Product card styling */
        .product-card {
            width: 260px;
            height: 400px;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            transition: all 0.2s ease;
            font-size: 16px;
            display: flex;
            flex-direction: column;
            margin: 10px;
            padding: 12px;
            background: #fff;
        }

        .product-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .product-img {
            width: 195px;
            height: 220px;
            object-position: center;
            background-color: #f8f9fa;
            margin-left: 18px;
        }

        .custom-line {
            height: 1px;
            background-color: #000;
            width: 100%;
            margin-bottom: 5px;
        }

        .price-new {
            color: #d9230f;
            font-weight: bold;
        }

        .price-old {
            text-decoration: line-through;
            color: gray;
        }

        .text-success.small {
            margin-left: 8px;
        }

        .wishlist-icon {
            color: red;
            float: right;
            font-size: 1.4rem;
        }

        .product-name {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 220px; /* limits width so ellipsis triggers */
            display: block;
        }

        /* Container fixed on top with transparent background and pointer-events none */
.search-bar-container {
    position: fixed;
    top: 43px; /* adjust for your topnav height */
    left: 12px;
    width: 100%;
    background: transparent;
    padding: 10px 15px;
    z-index: 1050;
    pointer-events: none; /* block clicks outside the form */
    display: flex;
    justify-content: center;
}

/* Enable pointer events on the form */
.search-bar-container form.search-input {
    pointer-events: auto; /* enable clicks inside form */
    display: flex;
    width: 500px; /* total width of search bar */
}

/* Input styles */
.search-input input[type="text"] {
    width: 350px;  /* input width */
    padding: 8px 20px;
    font-size: 1rem;
    border: 1px solid blue;
    border-right: none;
    border-radius: 25px 0 0 25px;
    outline: none;
}

/* Button styles */
.search-input button {
    width: 100px;  /* button width to fill remaining space */
    padding: 8px 20px;
    border: 1px solid blue;
    border-left: none;
    border-radius: 0 25px 25px 0;
    background-color: steelblue;
    color: white;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.3s ease;
}

.search-input button:hover {
    background-color: orchid;
    border-color: blue;
}
    </style>
</head>
<body>

<!-- Search bar container overlapping topnav -->
<div class="search-bar-container">
    <form method="get" action="" class="search-input">
        <input 
            type="text" 
            name="search" 
            placeholder="Search by brand or product name..." 
            value="<?= htmlspecialchars($searchTerm) ?>"
            aria-label="Search products"
			required
        />
        <button type="submit">Search</button>
    </form>
</div>

<div class="container mt-5 mb-3">
    <h4 class="mb-4">Search Results for: <strong><?= htmlspecialchars($searchTerm) ?></strong></h4>
    <div class="row">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($prod = $result->fetch_assoc()):
                $imageServerPath = __DIR__ . "/../Admin/product-images/" . $prod['image'];
                $imageUrlPath = "../Admin/product-images/" . htmlspecialchars($prod['image']);
                $imagePath = ($prod['image'] && file_exists($imageServerPath))
                    ? $imageUrlPath
                    : "https://via.placeholder.com/250x180?text=No+Image";

                $priceNew = $prod['discount_price'] > 0 ? $prod['discount_price'] : $prod['original_price'];
                $priceOld = ($prod['discount_price'] > 0 && $prod['discount_price'] < $prod['original_price']) ? $prod['original_price'] : null;

                $discountPercent = 0;
                if ($priceOld && $priceNew) {
                    $discountPercent = round(100 - (($priceNew / $priceOld) * 100));
                }
            ?>
            <div class="col-md-3 mb-4">
                <a href="../other-functions/product_details.php?id=<?= $prod['id'] ?>" class="text-decoration-none text-dark">
                    <div class="product-card shadow-sm">
                        <img src="<?= $imagePath ?>" class="product-img" alt="<?= htmlspecialchars($prod['product_name']) ?>" />
                        <div class="custom-line mt-3"></div>
                        <div class="p-3">
                            <?php
if (!function_exists('highlight')) {
    function highlight($text, $search) {
        if (!$search) return htmlspecialchars($text);
        $escapedSearch = preg_quote($search, '/'); 
        return preg_replace("/($escapedSearch)/i", '<strong>$1</strong>', htmlspecialchars($text));
    }
}

?>

<h6 class="mb-1 fw-bold"><?= highlight(strtoupper($prod['brand_name']), $searchTerm) ?></h6>
<p class="mb-1 text-muted small product-name"><?= highlight($prod['product_name'], $searchTerm) ?></p>

                            <div><span class="price-new">Php <?= number_format($priceNew, 2) ?></span></div>
                            <?php if ($discountPercent > 0): ?>
                                <div>
                                    <span class="price-old text-muted text-decoration-line-through">Php <?= number_format($priceOld, 2) ?></span>
                                    <span class="text-success small">-<?= $discountPercent ?>%</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <p class="text-muted">No products found matching "<strong><?= htmlspecialchars($searchTerm) ?></strong>".</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
