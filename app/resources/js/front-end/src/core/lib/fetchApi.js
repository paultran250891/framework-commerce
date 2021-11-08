export const fetchApi = async ({ data, url, method, callbackApi }, token) => {
    // console.log(data, url, token)
    const response = await fetch(url, {
        method: method ?? "POST",
        mode: "cors",
        cache: "no-cache",
        credentials: "same-origin",
        headers: { "Content-Type": "application/json" },
        redirect: "follow",
        referrerPolicy: "no-referrer",
        body: JSON.stringify({ ...data, token: token }),
    })
    // console.log(await response.text())
    callbackApi && callbackApi(response.text())

    return response.text();
}
