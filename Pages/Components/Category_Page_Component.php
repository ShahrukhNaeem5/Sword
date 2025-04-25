<?php
$base_url = '/Project'; // Adjust as needed
$show_heading = isset($show_heading) ? $show_heading : true; // Default to true if not set

// Fetch ONLY parent categories that are set to display and have products or subcategories with products
$parentQuery = "
    SELECT c.*, 
           (SELECT COUNT(*) FROM product_categories pc WHERE pc.category_id = c.category_id) as product_count,
           (SELECT COUNT(*) FROM categories sc 
            WHERE sc.parent_id = c.category_id 
            AND EXISTS (
                SELECT 1 FROM product_categories pc2 
                WHERE pc2.category_id = sc.category_id
            )) as subcategories_with_products
    FROM categories c
    WHERE c.parent_id = 0 AND c.display = 1
    HAVING product_count > 0 OR subcategories_with_products > 0
";
$parentResult = $conn->query($parentQuery);

$parentCategories = [];
while ($parent = $parentResult->fetch_assoc()) {
    // Fetch ONLY child categories that have products
    $childQuery = "
        SELECT c.*, 
               (SELECT COUNT(*) FROM product_categories pc WHERE pc.category_id = c.category_id) as product_count
        FROM categories c
        WHERE c.parent_id = " . $parent['category_id'] . "
        HAVING product_count > 0
    ";
    $childResult = $conn->query($childQuery);

    $children = [];
    while ($child = $childResult->fetch_assoc()) {
        $children[] = $child;
    }

    // Only include parent if it has children with products
    if (!empty($children)) {
        $parent['children'] = $children;
        $parentCategories[] = $parent;
    }
}
?>

<style>
    .slider-wrapper {
        position: relative;
        max-width: 100%;
        margin: auto;
        overflow: visible; /* Ensure buttons are not clipped */
    }

    .image-item-container {
        padding: 10px;
        background-color: #5d360f21;
    }

    .image-list-category {
        display: grid;
        grid-auto-flow: column;
        list-style: none;
        grid-auto-columns: calc((100% / 4) - 15px);
        gap: 20px;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        scroll-behavior: smooth;
        scrollbar-width: none;
        padding: 10px 0;
    }

    .image-list-category::-webkit-scrollbar {
        display: none;
    }

    .image-list-category .category-card {
        scroll-snap-align: start;
        display: flex;
        flex-direction: column;
        border: none;
        border-radius: 10px;
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
        height: 100%;
    }

    .image-list-category .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .image-list-category .category-card img {
        border-radius: 0 !important;
        object-fit: cover;
        height: 200px;
        width: 100%;
    }

    .image-list-category .category-card .card-body {
        background-color: #5d360f21;
        padding: 10px;
        text-align: left;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .image-list-category .category-card .card-title {
        font-size: 1.75rem;
        color: #603b29;
        margin-bottom: 0.25rem;
    }

    .image-list-category .category-card .card-text {
        font-size: 0.9rem;
        color: #603b29;
        margin-bottom: 1rem;
        flex-grow: 1;
    }

    .image-list-category .category-card .btn-explore {
        background-color: #603b29;
        color: white;
        border-radius: 0;
        width: 50%;
        padding: 0.5rem 1.5rem;
    }

    .image-list-category .category-card .btn-explore:hover {
        background-color: #4a2c1f;
    }

    .slide-button {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background-color: transparent !important;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 100; /* Increased to ensure visibility */
    }

    .slide-button i {
        color: #603b29 !important;
        font-size: 24px; /* Reduced for better fit on small screens */
    }

    .slide-button:hover {
        background: rgba(255, 255, 255, 1);
    }

    .slide-button#prev-slide {
        left: -100px; /* Default for large screens */
    }

    .slide-button#next-slide {
        right: -100px; /* Default for large screens */
    }

    .slider-scrollbar {
        height: 8px;
        width: 100%;
        display: flex;
        align-items: center;
        margin-top: 10px;
    }

    .scrollbar-track {
        height: 2px;
        width: 100%;
        background: #f1f1f1;
        position: relative;
        border-radius: 2px;
    }

    .scrollbar-thumb {
        height: 100%;
        width: 50%;
        background: #603b29;
        border-radius: 2px;
        position: absolute;
        cursor: grab;
    }

    .scrollbar-thumb:active {
        cursor: grabbing;
    }

    /* Responsive Adjustments */
    @media (max-width: 1200px) {
        .image-list-category {
            grid-auto-columns: calc((100% / 3) - 15px);
        }
        .slide-button#prev-slide {
            left: -60px; /* Adjusted for medium screens */
        }
        .slide-button#next-slide {
            right: -60px;
        }
    }

    @media (max-width: 992px) {
        .image-list-category {
            grid-auto-columns: calc((100% / 2) - 10px);
        }
        .slide-button#prev-slide {
            left: -40px;
        }
        .slide-button#next-slide {
            right: -40px;
        }
        .container-fluid.px-5 {
            padding-left: 2rem !important;
            padding-right: 2rem !important; /* Reduced padding */
        }
    }

    @media (max-width: 768px) {
        .image-list-category {
            grid-auto-columns: calc((100% / 2) - 10px);
        }
        .slide-button#prev-slide {
            left: -20px; /* Closer to carousel on small screens */
        }
        .slide-button#next-slide {
            right: -20px;
        }
        .container-fluid.px-5 {
            padding-left: 1rem !important;
            padding-right: 1rem !important; /* Further reduced padding */
        }
        .image-list-category .category-card .card-title {
            font-size: 1.5rem; /* Smaller title for better fit */
        }
        .image-list-category .category-card img {
            height: 150px; /* Smaller image height */
        }
    }

    @media (max-width: 576px) {
        .image-list-category {
            grid-auto-columns: calc(100% - 10px); /* Full-width cards */
        }
        .slide-button#prev-slide {
            left: 10px; /* Inside the carousel for very small screens */
            background: rgba(255, 255, 255, 0.8); /* Visible background */
        }
        .slide-button#next-slide {
            right: 10px;
            background: rgba(255, 255, 255, 0.8);
        }
        .container-fluid.px-5 {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important; /* Minimal padding */
        }
        .image-list-category .category-card .card-title {
            font-size: 1.25rem;
        }
        .image-list-category .category-card .card-text {
            font-size: 0.8rem;
        }
        .image-list-category .category-card .btn-explore {
            width: 100%; /* Full-width button */
            padding: 0.4rem 1rem;
        }
        .slide-button {
            width: 32px;
            height: 32px; /* Smaller buttons */
        }
        .slide-button i {
            font-size: 18px;
        }
    }
</style>

<!-- Dynamic carousel generation -->
<?php foreach ($parentCategories as $index => $parent): ?>
    <div class="category-list mb-5 category-<?php echo $index + 1; ?>">
        <div class="container-fluid px-5">
            <?php if ($show_heading): ?>
                <h1 class="text-center mt-5 mb-4 heading-main">
                    Categories of <?php echo htmlspecialchars($parent['name']); ?>
                </h1>
            <?php endif; ?>

            <div class="slider-wrapper">
                <button id="prev-slide" class="slide-button material-symbols-rounded">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <ul class="image-list-category">
                    <?php foreach ($parent['children'] as $child): ?>
                        <li class="image-item-container">
                                <div class="card category-card">
                                    <img src="<?php echo $base_url . '/Assets/uploads/categories/' . htmlspecialchars($child['image']); ?>"
                                        class="card-img-top" alt="<?php echo htmlspecialchars($child['name']); ?>">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($child['name']); ?></h5>
                                        <p class="card-text">
                                            <?php echo isset($child['description']) ? htmlspecialchars(substr($child['description'], 0, 100)) . '...' : 'Explore our range of ' . htmlspecialchars($child['name']) . ' products.'; ?>
                                        </p>
                                        <a href="<?php echo $base_url . '/Pages/Categorized_products.php?category_id=' . htmlspecialchars($child['category_id']); ?>"
                                            class="btn btn-explore">Explore</a>
                                    </div>
                                </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <button id="next-slide" class="slide-button material-symbols-rounded">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <div class="slider-scrollbar">
                <div class="scrollbar-track">
                    <div class="scrollbar-thumb"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for this carousel -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const initSlider = () => {
                const imageList = document.querySelector(".category-<?php echo $index + 1; ?> .slider-wrapper .image-list-category");
                const slideButtons = document.querySelectorAll(".category-<?php echo $index + 1; ?> .slider-wrapper .slide-button");
                const sliderScrollbar = document.querySelector(".category-<?php echo $index + 1; ?> .slider-scrollbar");
                const scrollbarThumb = sliderScrollbar.querySelector(".scrollbar-thumb");
                const maxScrollLeft = imageList.scrollWidth - imageList.clientWidth;

                // Handle scrollbar thumb drag
                scrollbarThumb.addEventListener("mousedown", (e) => {
                    const startX = e.clientX;
                    const thumbPosition = scrollbarThumb.offsetLeft;
                    const maxThumbPosition = sliderScrollbar.getBoundingClientRect().width - scrollbarThumb.offsetWidth;

                    const handleMouseMove = (e) => {
                        const deltaX = e.clientX - startX;
                        const newThumbPosition = thumbPosition + deltaX;
                        const boundedPosition = Math.max(0, Math.min(maxThumbPosition, newThumbPosition));
                        const scrollPosition = (boundedPosition / maxThumbPosition) * maxScrollLeft;

                        scrollbarThumb.style.left = `${boundedPosition}px`;
                        imageList.scrollLeft = scrollPosition;
                    }

                    const handleMouseUp = () => {
                        document.removeEventListener("mousemove", handleMouseMove);
                        document.removeEventListener("mouseup", handleMouseUp);
                    }

                    document.addEventListener("mousemove", handleMouseMove);
                    document.addEventListener("mouseup", handleMouseUp);
                });

                // Slide images according to the slide button clicks
                slideButtons.forEach(button => {
                    button.addEventListener("click", () => {
                        const direction = button.id === "prev-slide" ? -1 : 1;
                        const scrollAmount = imageList.clientWidth * direction;
                        imageList.scrollBy({ left: scrollAmount, behavior: "smooth" });
                    });
                });

                const handleSlideButtons = () => {
                    slideButtons[0].style.display = imageList.scrollLeft <= 0 ? "none" : "flex";
                    slideButtons[1].style.display = imageList.scrollLeft >= maxScrollLeft ? "none" : "flex";
                }

                const updateScrollThumbPosition = () => {
                    const scrollPosition = imageList.scrollLeft;
                    const thumbPosition = (scrollPosition / maxScrollLeft) * (sliderScrollbar.clientWidth - scrollbarThumb.offsetWidth);
                    scrollbarThumb.style.left = `${thumbPosition}px`;
                }

                imageList.addEventListener("scroll", () => {
                    updateScrollThumbPosition();
                    handleSlideButtons();
                });

                // Initialize button visibility
                handleSlideButtons();

                // Update on resize
                window.addEventListener("resize", () => {
                    updateScrollThumbPosition();
                    handleSlideButtons();
                });
            }

            initSlider();
        });
    </script>
<?php endforeach; ?>