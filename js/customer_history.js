function toggleDetails(orderId) {
    const detailsElement = document.getElementById(`details-${orderId}`);
    if (detailsElement.style.display === "none" || detailsElement.style.display === "") {
        detailsElement.style.display = "block";
    } else {
        detailsElement.style.display = "none";
    }
}
