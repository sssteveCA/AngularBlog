import AddCommentInterface from "src/interfaces/requests/article/comment/addcomment.interface";

export default class AddComment{
    private _comment_text: string;
    private _permalink: string;
    private _token_key: string;
    private _url: string;

    constructor(data: AddCommentInterface){
        this._comment_text = data.comment_text;
        this._permalink = data.permalink;
        this._token_key = data.token_key;
        this._url = data.url;
    }

    get comment_text(){return this._comment_text;}
    get permalink(){return this._permalink;}
    get token_key(){return this._token_key;}
    get url(){return this._url;}
}