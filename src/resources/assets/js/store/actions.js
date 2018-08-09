import * as api from '../api'
import * as types from './types'
import alertify from 'alertifyjs'
alertify.set('notifier', 'position', 'top-right')

const withPwd = (state, path) => {
    return state.path.replace(/^\/|\/$/g, '') + '/' + path
}
const basename = path => {
    return path.split('/').pop()
}

let basePath = '/filevuer'

export default {

    /**
     * Check to see if there is a valid session, 
     * if not reload the window to log the user out.
     */
    [types.POLL_CONNECTION]({
        commit
    }) {
        return api.poll().then(response => {
            if(response.active == false) {
                window.location.reload(true);
            }
        })
    },

    [types.SET_CONNECTION]({
        commit
    }, selectedConnection) {
        return api.setConnection(selectedConnection).then(response => {
            if (response === false) {
                alertify.error('Server login failed!')
                return false
            }
            return true
        })
    },

    [types.FETCH_FILES]({
        commit
    }, path) {
        commit(types.SET_LOADING, true)
        commit(types.SET_PATH, path)

        api.getFiles(path).then(files => {
            commit(types.SET_FILELIST, files)
            commit(types.SET_LOADING, false)
        }).catch(e => {
            alertify.error('Failed to fetch files')
            commit(types.SET_LOADING, false)
        })
    },

    [types.FETCH_CONTENTS]({
        commit,
        dispatch,
        state
    }, path) {
        commit(types.SET_LOADING, true)
        path = withPwd(state, path)

        api.getContents(path).then(response => {
            if (response.download === true) {
                const a = window.document.createElement('a')
                a.href = 'data:application/octet-stream;base64,' + response.contents
                a.download = basename(path)
                document.body.appendChild(a)
                a.click()
                document.body.removeChild(a)
            } else {
                commit(types.SET_OPEN_FILE, path)
                commit(types.SET_EDITOR_CONTENTS, response.contents)
                commit(types.SET_EDITOR_VISIBILITY, true)
            }

            commit(types.SET_LOADING, false)
        }).catch(e => {
            alertify.error('Failed to fetch file')
            commit(types.SET_LOADING, false)
        })
    },

    [types.PUT_CONTENTS]({
        dispatch,
        commit,
        state
    }, contents) {
        commit(types.SET_LOADING, true)

        api.putContents(state.openFile, contents).then(() => {
            alertify.success('Successfully updated file')
            dispatch(types.REFRESH)
        }).catch(e => {
            alertify.error('Failed to update file')
            commit(types.SET_LOADING, false)
        })
    },

    [types.DELETE_SELECTED]({
        dispatch,
        commit,
        state
    }) {
        let files = state.files.filter(file => file.checked)

        let cleanUp = () => {
            //   commit(types.TOGGLE_MODAL, 'confirmDelete')
            commit(types.SET_LOADING, false)
        }

        if (files.length < 1) {
            cleanUp()
            return false
        }

        commit(types.SET_LOADING, true)

        api.deleteFiles(files).then(() => {
            alertify.success('Files deleted')
            dispatch(types.REFRESH)
            cleanUp()
        }).catch(e => {
            alertify.error('Failed to delete file(s)')
            cleanUp()
        })
    },

    [types.DOWNLOAD_SELECTED]({
        dispatch,
        commit,
        state
    }) {
        let files = state.files.filter(file => file.checked)

        let cleanUp = () => {
            commit(types.SET_LOADING, false)
        }

        commit(types.SET_LOADING, false)
        if (files.length < 1) {
            cleanUp()
            return false
        }

        commit(types.SET_LOADING, true)

        api.download(files).then(hash => {
            location.assign(basePath + '/download/' + hash)
            dispatch(types.TOGGLE_ALL, false)
            alertify.success('Download started successfully')
            cleanUp()
        }).catch(e => {
            alertify.error('Failed to download files')
            cleanUp()
        })
    },
    [types.CREATE_NEW]({
        dispatch,
        commit,
        state
    }, {
        type,
        path
    }) {
        commit(types.SET_LOADING, true)
        path = withPwd(state, path)

        api.create(type, path).then(() => {
            let newtype = type === 'file' ? 'New File' : 'New Folder'
            alertify.success(newtype + ' created')
            dispatch(types.REFRESH)
        }).catch(e => {
            alertify.error('Failed to create ' + type)
        })
    },

    [types.UPLOAD]({
        commit,
        state,
        dispatch
    }, {
        files,
        extract
    }) {
        commit(types.SET_LOADING, true)

        return api.upload(files, state.path, extract).then(() => {
            alertify.success('Upload successful')
            dispatch(types.REFRESH)
        }).catch(response => {
            if (response.status === 422) {
                alertify.error('The uploaded file is too large')
            } else {
                alertify.error('Failed to upload files')
            }
            dispatch(types.REFRESH)
        })
    },

    [types.TOGGLE_ALL]({
        commit,
        state
    }, newState) {
        commit(types.SET_ALL_SELECTED, newState)
        state.files.forEach(file => {
            commit(types.TOGGLE_FILE, {
                file,
                newState
            })
        })
    },

    [types.CHANGE_DIRECTORY]({
        state,
        dispatch
    }, path) {
        dispatch(types.FETCH_FILES, path)
    },

    [types.CHANGE_DIRECTORY_RELATIVE]({
        state,
        dispatch
    }, path) {
        path = state.path += path + '/'
        dispatch(types.CHANGE_DIRECTORY, path)
    },

    [types.REFRESH]({
        state,
        dispatch
    }) {
        dispatch(types.FETCH_FILES, state.path)
    },

    [types.LEVEL_UP]({
        dispatch,
        state
    }) {
        let path = state.path.replace(/^\/|\/$/g, '').split('/')
        path.pop()

        dispatch(types.CHANGE_DIRECTORY, path.length > 0 ? '/' + path.join('/') + '/' : '/')
    },

    [types.UPDATE_FILELIST]({
        commit
    }, files) {
        commit(types.SET_LOADING, false)
        commit(types.SET_ALL_SELECTED, false)
        commit(types.SET_FILELIST, files)
    }
}