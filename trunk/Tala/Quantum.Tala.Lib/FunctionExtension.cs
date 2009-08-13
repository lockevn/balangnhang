using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using MySql.Data.Types;
using System.Collections;

namespace Quantum.Tala.Lib
{
    /// <summary>
    /// Cài đặt các Extension Function dùng chung để tiện cho viết mã
    /// </summary>
    public static class FunctionExtension
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

        /// <summary>
        /// Chỉ khi S là chuỗi "1" mới trả về true. Các trường hợp khác (kể cả null) trả về false
        /// </summary>
        /// <param name="s"></param>
        /// <returns></returns>
        public static bool String01ToBoolSafety(this string s)
        {
            return (s ?? "0") == "1" ? true : false;
        }

        public static string ToUTCString(this DateTime dtm)
        {
            return GURUCORE.Lib.Core.Text.TextHelper.DateTimeToUTCString(dtm);
        }

        public static string ToUTCString(this MySqlDateTime dtm)
        {
            return GURUCORE.Lib.Core.Text.TextHelper.DateTimeToUTCString((DateTime)dtm);
        }

    
        /// <summary>
        /// Sử dụng linq để phân trang. 
        /// VD: listSoi.Page(0, 20),   
        /// listSoi.Page(APIParamHelper.GetPagingPage(), APIParamHelper.GetPagingItemPerPage())
        /// </summary>
        /// <typeparam name="TSource"></typeparam>
        /// <param name="source"></param>
        /// <param name="page">start from 0</param>
        /// <param name="itemperpage"></param>
        /// <returns></returns>
        public static IEnumerable<TSource> Page<TSource>(this IEnumerable<TSource> source, int page, int itemperpage)
        {
            return source.Skip(page * itemperpage).Take(itemperpage);
        }

        public static bool IsNullOrEmpty<T>(this IEnumerable<T> source)
        {
            if (null == source)
            {
                return true;
            }
            if (source.Count() <= 0)
            {
                return true;
            }
            return false;
        }

    }
}