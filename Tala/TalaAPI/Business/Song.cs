using System;
using System.Collections;
using System.Collections.Generic;
using System.Data;
using System.Configuration;
using System.Linq;
using System.Xml.Linq;

namespace TalaAPI.Business
{
    /// <summary>
    /// Thread-safe singleton example without using locks
    /// </summary>
    public sealed class Song
    {
        private static readonly Song instance = new Song();

        // Explicit static constructor to tell C# compiler
        // not to mark type as beforefieldinit
        static Song()
        {
        }

        Song()
        {
        }

        /// <summary>
        /// The public Instance property to use
        /// </summary>
        public static Song Instance
        {
            get { return instance; }
        }

        public string Text;
        
        public Dictionary<string, string> ValidAuthkey = new Dictionary<string, string>();
        public Dictionary<string, User> OnlineUser = new Dictionary<string,User>();
        public Dictionary<string, Soi> Soi = new Dictionary<string,Soi>();

        public Soi GetSoiByID(string soiid)
        {            
            return Soi[soiid];            
        }

        /// <summary>
        /// 
        /// </summary>
        /// <param name="authkey"></param>
        /// <returns>null if can not find</returns>
        public string GetUsernameByAuthkey(string authkey)
        {
            string ret = string.Empty;
            if (ValidAuthkey.TryGetValue(authkey, out ret))
            {
                return ret;
            }
            else
            {
                return string.Empty;
            }            
        }

        public User GetUserByUsername(string username)
        {
            User ret;
            OnlineUser.TryGetValue(username, out ret);
            return ret;
        }

        public User GetUserByAuthkey(string authkey)
        {
            return GetUserByUsername(GetUsernameByAuthkey(authkey));
        }

    }
}
