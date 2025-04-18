"use client"

export const apiLink = "http://localhost:9000/INFO263-25S1/Skeleton/imdb-php/api.php";
const getRequestInfo = {
    method: "GET",
    headers: {
        "Content-Type": "application/json",
    },
    mode: "cors",
    cache: "default",
}

export async function fetchTitleById(id) {
    const link = apiLink + '?q=title&id=' + id;
    let res = await fetch(link);
    return await res.json();
}

export async function fetchTitles(offset, limit) {
    if (!offset) {
        offset = 0;
    }
    if (!limit) {
        limit = 8;
    }

    let link = apiLink + '?q=titles&offset=' + offset + '&limit=' + limit;
    let res = await fetch(link);
    return await res.json();
}

export async function fetchTitleCount() {
    let link = apiLink + "?q=title_count";
    let res = await fetch(link);
    return await res.json();
}