
/**
 * Mail contacts POST data
 */
export type ContactsParams = {
    email: string;
    message: string;
    subject: string;
}

/**
 * Delete logged account params
 */
export type DaParams = {
    conf_password: string;
    password: string;
}

/**
 * Edit logged account name and surname params
 */
export type EnParams = {
    name: string;
    surname: string;
}

/**
 * Edit logged account password params
 */
export type EpParams = {
    conf_new_password: string;
    new_password: string;
    old_password: string;
}

/**
 * Edit logged username params
 */
export type EuParams = {
    password: string;
    username: string;
}