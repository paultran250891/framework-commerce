

export const api = async ({ data, url, method, callbackApi }) => {

    const token = await fetch('/token')
    const response = await fetch(url, {
        method: method ?? 'POST',
        mode: 'cors',
        cache: 'no-cache',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json' },
        redirect: 'follow',
        referrerPolicy: 'no-referrer',
        body: JSON.stringify({ ...data, token: await token.json() })
    });

    // console.log(response)

    callbackApi && callbackApi(response.json())


    return response.json();
}


