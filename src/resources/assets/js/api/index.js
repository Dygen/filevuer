import axios from 'axios'

let http = axios
let basePath = '/filevuer'

const removeSlashes = (str) => str.replace(/^\/|\/$/g, '')

export function poll () {
    return http({
      url: basePath + '/poll',
      method: 'get',
    }).then(response => {
      return response.data
    })
}

export function setConnection (selected) {
  return http({
    url: basePath,
    method: 'post',
    data: {
      connection: selected
    }
  }).then(response => {
    return response.data
  })
}

export function getFiles (path) {
  return http({
    url: basePath + '/directory/?path=' + removeSlashes(path),
    method: 'GET'
  }).then(response => {
    response.data.listing.map(item => {
      item.checked = false
      item._uid = +Date.now()

      return item
    })

    return response.data.listing
  })
}

export function getContents (path) {
  return http({
    url: basePath + '/file/?path=' + removeSlashes(path),
    method: 'GET'
  })
    .then(response => response.data)
}

export function putContents (path, contents) {
  path = removeSlashes(path)
  return http({
    url: basePath + '/file',
    method: 'put',
    data: {
      path,
      contents
    }
  })
    .then(response => response.data)
}

export function deleteFiles (entries) {
  const files = entries.filter(entry => entry.type === 'file').map(entry => entry.path)
  const directories = entries.filter(entry => entry.type === 'dir').map(entry => entry.path)

  return Promise.all([
    http({
      url: basePath + '/file',
      method: 'delete',
      data: {
        path: files
      }
    }),
    http({
      url: basePath + '/directory',
      method: 'delete',
      data: {
        path: directories
      }
    })
  ])
}

export function create (type, path) {
  return http({
    url: basePath + '/' + type,
    method: 'post',
    data: {
      path
    }
  })
}

export function download (path) {
  return http({
    url: basePath + '/download',
    method: 'post',
    data: {
      path
    }
  }).then(response => response.data)
}

export function upload (files, path, extract) {
  let data = new FormData()
  for (let i = 0; i < files.length; i++) {
    data.append('files[]', files[i])
  }

  data.append('path', path)
  data.append('extract', extract ? 1 : 0)

  return http({
    url: basePath + '/upload',
    method: 'post',
    data: data
  })
}
