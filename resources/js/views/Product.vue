<template>
    <div class="container">
        <div v-if="product" class="product-container">
            <div class="image-section">
                <img
                    :src="selectedImage"
                    alt="Product Image"
                    class="main-image"
                />
                <div class="thumbnail-list">
                    <img
                        v-for="(img, index) in product.images"
                        :src="img"
                        :key="index"
                        :class="{
                            thumbnail: true,
                            active: selectedImage === img,
                        }"
                        @click="updateSelectedImage(img)"
                        alt="Thumbnail"
                    />
                </div>
            </div>

            <div class="details-section">
                <div class="discount-tag">{{ product.discount.amount }}%</div>
                <h1 class="product-title">{{ product.name }}</h1>
                <p class="product-description">{{ product.description }}</p>
                <div class="price-info">
                    <span class="discounted-price"
                        >$ {{ product.price.discounted }}</span
                    >
                    <span class="full-price">$ {{ product.price.full }}</span>
                </div>
                <button class="add-to-cart-btn">Add to Cart</button>
            </div>
        </div>

        <div v-else-if="error" class="error-state">
            <h1>404 - Product Not Found</h1>
            <p>{{ error }}</p>
            <router-link to="/" class="home-link">Go Back Home</router-link>
        </div>

        <div v-else class="loading">
            <p>Loading...</p>
        </div>
    </div>
</template>

<script>
import axios from "axios";
export default {
    name: "Product",
    data() {
        return {
            product: null,
            selectedImage: "",
            error: null,
        };
    },
    methods: {
        updateSelectedImage(image) {
            this.selectedImage = image;
        },
    },
    async created() {
        try {
            const { slug } = this.$route.params;
            const response = await axios.get(`/client/products/${slug}`);
            this.product = response.data.data || response.data;
            if (this.product.images && this.product.images.length > 0) {
                this.selectedImage = this.product.images[0];
            }
        } catch (err) {
            if (err.response && err.response.status === 404) {
                this.error = "The product you are looking for does not exist.";
            } else {
                console.error("Error fetching product:", err);
                this.error = "An error occurred while fetching the product.";
            }
        }
    },
};
</script>

<style scoped>
.container {
    max-width: 1440px;
    margin: 0 auto;
    padding: 2rem;
    background-color: hsl(0, 0%, 100%);
    font-family: "Helvetica Neue", Arial, sans-serif;
}

.product-container {
    display: flex;
    gap: 2rem;
    align-items: flex-start;
}

.image-section {
    flex: 1;
    background-color: hsl(0, 0%, 100%);
    padding: 1rem;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
.main-image {
    width: 100%;
    border-radius: 10px;
    object-fit: cover;
    display: block;
}
.thumbnail-list {
    margin-top: 1rem;
    display: flex;
    gap: 1rem;
}
.thumbnail {
    width: 80px;
    height: 80px;
    border-radius: 8px;
    object-fit: cover;
    cursor: pointer;
    opacity: 0.7;
    transition: opacity 0.3s, transform 0.3s;
    border: 2px solid transparent;
}
.thumbnail:hover {
    opacity: 1;
    transform: scale(1.05);
}
.thumbnail.active {
    border-color: hsl(26, 100%, 55%);
    opacity: 1;
}

.details-section {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    padding: 1rem;
}

.discount-tag {
    background-color: hsl(25, 100%, 94%);
    color: hsl(26, 100%, 55%);
    font-weight: 700;
    font-size: 0.9rem;
    padding: 0.4rem 0.8rem;
    border-radius: 4px;
    width: fit-content;
    letter-spacing: 1px;
}

.product-title {
    font-size: 2.5rem;
    color: hsl(220, 13%, 13%);
    margin: 0;
}

.product-description {
    font-size: 16px;
    line-height: 1.6;
    color: hsl(220, 14%, 75%);
    margin-bottom: 1rem;
}

.price-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}
.discounted-price {
    font-size: 2rem;
    font-weight: 700;
    color: hsl(26, 100%, 55%);
}
.full-price {
    font-size: 1.2rem;
    text-decoration: line-through;
    color: hsl(219, 9%, 45%);
}

.add-to-cart-btn {
    background-color: hsl(26, 100%, 55%);
    color: hsl(0, 0%, 100%);
    border: none;
    padding: 1.2rem 2rem;
    font-size: 1rem;
    font-weight: 700;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s;
    letter-spacing: 0.5px;
    max-width: 300px;
}
.add-to-cart-btn:hover {
    background-color: hsl(26, 100%, 45%);
}

.error-state {
    text-align: center;
    padding: 2rem;
    background-color: hsl(223, 64%, 98%);
    border-radius: 8px;
    margin-top: 2rem;
}
.error-state h1 {
    font-size: 2rem;
    color: hsl(220, 13%, 13%);
    margin-bottom: 1rem;
}
.home-link {
    display: inline-block;
    margin-top: 1rem;
    font-size: 1.2rem;
    color: hsl(26, 100%, 55%);
    text-decoration: none;
    font-weight: bold;
    border: 2px solid hsl(26, 100%, 55%);
    padding: 0.5rem 1rem;
    border-radius: 8px;
    transition: background-color 0.3s, color 0.3s;
}
.home-link:hover {
    background-color: hsl(26, 100%, 55%);
    color: hsl(0, 0%, 100%);
}

.loading {
    text-align: center;
    font-size: 16px;
    color: hsl(220, 14%, 75%);
}

@media screen and (max-width: 768px) {
    .product-container {
        flex-direction: column;
    }
    .image-section,
    .details-section {
        width: 100%;
    }
    .thumbnail {
        width: 60px;
        height: 60px;
    }
    .add-to-cart-btn {
        width: 100%;
        text-align: center;
    }
}
</style>
