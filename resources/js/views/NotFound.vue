<template>
    <div class="not-found">
        <h1>404</h1>
        <p>Oops! The page you are looking for doesn't exist.</p>
        <router-link to="/" class="home-link">Go back to Home</router-link>
    </div>
</template>

<script>
export default {
    name: "NotFound",
};
</script>

<style scoped>
.not-found {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: hsl(223, 64%, 98%);
    text-align: center;
    padding: 0 1rem;
}
.not-found h1 {
    font-size: 5rem;
    color: hsl(220, 13%, 13%);
    margin: 0;
}
.not-found p {
    font-size: 1.5rem;
    color: hsl(220, 14%, 75%);
    margin: 1rem 0;
}
.home-link {
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
</style>
