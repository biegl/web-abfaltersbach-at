const global = window as any

export default function authHeader() {
  let user = JSON.parse(global.localStorage.getItem('user'))

  if (user && user.accessToken) {
    return { Authorization: 'Bearer ' + user.access_token }
  } else {
    return {}
  }
}
