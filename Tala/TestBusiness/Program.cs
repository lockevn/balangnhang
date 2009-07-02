using System;
using System.Collections.Generic;

using Quantum.Tala.Service.Authentication;
using Quantum.Tala.Lib.XMLOutput;
using Quantum.Tala.Service.Business;
using System.Xml.Linq;
using System.Threading;
using System.Text;


namespace TestBusiness
{
    class MyClass : APIDataEntry
    {
        [ElementXMLExportAttribute("", DataOutputXMLType.Attribute)]
        public string EmptyProperty
        {
            get { return ""; }
        }

        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public string FieldProperty
        {
            get { return "nestedvalue"; }
        }


        [ElementXMLExportAttribute("", DataOutputXMLType.Attribute)]
        public string AttribFieldProperty
        {
            get { return "atrribvalue"; }
        }

        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public Card MyCard
        {
            get 
            {
                Card c = new Card("13", "c");
                return c;
            }
        }


        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag, DataListizeType.ListGeneric, false, false)]
        public List<Card> ListOfCard
        {
            get
            {
                List<Card> list = new List<Card>();
                Card c1 = new Card("01", "c");
                Card c2 = new Card("02", "c");
                list.Add(c1);
                list.Add(c2);

                return list;
            }
        }

        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag, DataListizeType.Array, false, true)]
        public Card[] CardArray
        {
            get
            {                
                Card c1 = new Card("11", "c");
                Card c2 = new Card("12", "c");
                Card[] array = { c1, c2};

                return array;
            }
        }
    }


    class Program
    {        
        /*static void Main(string[] args)
        {
            #region Test performance with XElement and StringBuilder
            
            // StringBuilder faster 2x with Xelement, but not signical
            
            //XElement superX = new XElement("root");
            //StringBuilder superString = new StringBuilder();

            //DateTime start;
            //DateTime end;
            //TimeSpan elap;
            
            //start = DateTime.Now;
            //for (int i = 0; i < 500000; i++)
            //{
            //    XElement x = new XElement("q",
            //        new XAttribute("a1", "abc"),
            //        new XAttribute("a2", "abc"),
            //        new XAttribute("a3", "abc"),
            //        new XAttribute("a4", "abc"),

            //        new XElement("e1", "abc"),
            //        new XElement("e2", "abc"),
            //        new XElement("e3", "abc"),
            //        new XElement("e4", "abc"),
            //        new XElement("e5", "abc"),
            //        new XElement("e",
            //            new XElement("ec1", "abc"),
            //            new XElement("ec2", "abc"),
            //            new XElement("ec3", "abc"),
            //            new XElement("ec4", "abc")
            //            )
            //        );
            //    superX.Add(x);//x.ToString();
            //}
            
            //end = DateTime.Now;
            //elap = end.Subtract(start);
            //Console.WriteLine(elap.TotalMilliseconds);

            //start = DateTime.Now;
            ////superX.ToString();
            //end = DateTime.Now;
            //elap = end.Subtract(start);
            //Console.WriteLine(elap.TotalMilliseconds);


            //Thread.Sleep(1000);



            //start = DateTime.Now;
            //for (int i = 0; i < 500000; i++)
            //{
            //    string s1 = "<e1>abc</e1><e1>abc</e1><e1>abc</e1><e1>abc</e1><e1>abc</e1>";
            //    string s2 = "<e><ec1>abc</ec1><ec1>abc</ec1><ec1>abc</ec1><ec1>abc</ec1><e>";
            //    string s = string.Format(
            //        "<q a1='{2}' a2='{3}' a3='{4}' a4='{5}' >{0}{1}</q>",
            //        s1, s2, "abc", "abc", "abc", "abc"
            //        );
            //    superString.Append(s);
            //}
            //superString.ToString();
            //end = DateTime.Now;
            //elap = end.Subtract(start);
            //Console.WriteLine(elap.TotalMilliseconds);

            #endregion


            //IUser u = AuthenticationProvider.Authenticate("*","vu1","quantum");            
            //Console.WriteLine(u.Username);
            //IUser u2 = AuthenticationProvider.Authenticate("*", "v2", "quantum");
            //Console.WriteLine(u2.Username);

            Card cc = new Card("01c", "");
            Card[] arrayc = { cc };
            List<Card> listc = new List<Card>();
            listc.Add(cc);


            Console.ReadLine();
        }*/

        /// <summary>
        /// Test U
        /// </summary>
        /// <param name="args"></param>
        static void Main(string[] args)
        {
            List<Card> cardList = new List<Card>();

            //Card card = new Card("02", "c");
            //cardList.Add(card);
            //card = new Card("02", "d");
            //cardList.Add(card);
            //card = new Card("02", "p");
            //cardList.Add(card);
            //card = new Card("02", "t");
            //cardList.Add(card);
            //card = new Card("03", "d");
            //cardList.Add(card);
            //card = new Card("04", "d");
            //cardList.Add(card);
            //card = new Card("05", "d");
            //cardList.Add(card);
            //card = new Card("06", "d");
            //cardList.Add(card);
            //card = new Card("05", "c");
            //cardList.Add(card);
            //card = new Card("05", "p");
            //cardList.Add(card);

            Card card = new Card("01", "t");
            cardList.Add(card);
            card = new Card("02", "c");
            cardList.Add(card);
            card = new Card("02", "d");
            cardList.Add(card);
            card = new Card("02", "p");
            cardList.Add(card);
            card = new Card("02", "t");
            cardList.Add(card);
            card = new Card("03", "t");
            cardList.Add(card);
            card = new Card("04", "t");
            cardList.Add(card);
            card = new Card("05", "c");
            cardList.Add(card);
            card = new Card("05", "d");
            cardList.Add(card);
            card = new Card("05", "t");
            cardList.Add(card);

            int count = UUtil.CheckU(cardList);
            System.Console.Out.WriteLine("========== U result: " + count + "====== ");                                   

            Console.ReadLine();
        }

        
    }
}
