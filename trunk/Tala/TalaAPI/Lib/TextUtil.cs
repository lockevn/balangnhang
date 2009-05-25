using System;
using System.Data;
using System.Configuration;
using System.Linq;
using System.Web;
using System.Web.Security;
using System.Web.UI;
using System.Web.UI.HtmlControls;
using System.Web.UI.WebControls;
using System.Web.UI.WebControls.WebParts;
using System.Xml.Linq;
using System.Collections.Generic;

namespace TalaAPI.Lib
{
    public static class TextUtil
    {
        public static string GetRandomGUID()
        {
            return Guid.NewGuid().ToString();
        }

        public static int[] ReindexArrayRandomly(int[] inputArr)
        {
            List<int> tmpList = new List<int>();
            List<int> returnList = new List<int>();
            /*create a list to store values in inputArr*/
            for (int i = 0; i < inputArr.Length; i++)
            {
                tmpList.Add(inputArr[i]);
            }
            Random randomGenerator = new Random();

            while (tmpList.Count > 0)
            {
                /*randomly generate an index from 0 to tmpList.count to pick a value from tmpList*/
                int randomIndex = randomGenerator.Next(0, tmpList.Count);
                /*add to returnList*/
                returnList.Add(tmpList.ElementAt(randomIndex));
                /*remove item at the random index*/
                tmpList.RemoveAt(randomIndex);
            }
            return returnList.ToArray();
        }



        /// <summary>
        /// try to cast to string, not raise exception
        /// </summary>
        /// <param name="s"></param>
        /// <returns>return string.empty</returns>
        public static string ToStringSafety(this string s)
        {
            if (string.IsNullOrEmpty(s))
            {
                return string.Empty;
            }
            else
            {
                return s;
            }
            
        }

        /// <summary>
        /// return s.ToStringSafety().Trim().ToLower()
        /// </summary>
        /// <param name="s"></param>
        /// <returns></returns>
        public static string ToStringSafetyNormalize(this string s)
        {
            return s.ToStringSafety().Trim().ToLower();
        }

        public static bool IsNullOrEmpty(this string s)
        {
            return string.IsNullOrEmpty(s);
        }

    }
}
