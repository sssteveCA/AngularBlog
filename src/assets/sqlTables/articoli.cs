using System;
using System.Collections;
using System.Collections.Generic;
using System.Text;
namespace Stefano
{
    #region Articoli
    public class Articoli
    {
        #region Member Variables
        protected int _id;
        protected string _title;
        protected int _author;
        protected string _permalink;
        protected unknown _content;
        protected string _introtext;
        protected string _categories;
        protected string _tags;
        protected DateTime _creation_time;
        protected DateTime _last_modified;
        #endregion
        #region Constructors
        public Articoli() { }
        public Articoli(string title, int author, string permalink, unknown content, string introtext, string categories, string tags, DateTime creation_time, DateTime last_modified)
        {
            this._title=title;
            this._author=author;
            this._permalink=permalink;
            this._content=content;
            this._introtext=introtext;
            this._categories=categories;
            this._tags=tags;
            this._creation_time=creation_time;
            this._last_modified=last_modified;
        }
        #endregion
        #region Public Properties
        public virtual int Id
        {
            get {return _id;}
            set {_id=value;}
        }
        public virtual string Title
        {
            get {return _title;}
            set {_title=value;}
        }
        public virtual int Author
        {
            get {return _author;}
            set {_author=value;}
        }
        public virtual string Permalink
        {
            get {return _permalink;}
            set {_permalink=value;}
        }
        public virtual unknown Content
        {
            get {return _content;}
            set {_content=value;}
        }
        public virtual string Introtext
        {
            get {return _introtext;}
            set {_introtext=value;}
        }
        public virtual string Categories
        {
            get {return _categories;}
            set {_categories=value;}
        }
        public virtual string Tags
        {
            get {return _tags;}
            set {_tags=value;}
        }
        public virtual DateTime Creation_time
        {
            get {return _creation_time;}
            set {_creation_time=value;}
        }
        public virtual DateTime Last_modified
        {
            get {return _last_modified;}
            set {_last_modified=value;}
        }
        #endregion
    }
    #endregion
}