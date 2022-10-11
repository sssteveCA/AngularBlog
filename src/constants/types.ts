
/**
 * Delete logged account params
 */
export type DaParams = {
    conf_password: string;
    password: string;
}

/**
 * Edit logged account password params
 */
export type EpParams = {
    conf_new_password: string;
    new_password: string;
    old_password: string;
};