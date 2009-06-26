using System;
using System.Collections.Generic;

using Quantum.Tala.Service.Authentication;
using Quantum.Tala.Lib.XMLOutput;
using Quantum.Tala.Service.Business;


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
        static void Main(string[] args)
        {
            IUser u = AuthenticationProvider.Authenticate("*","vu1","quantum");            
            Console.WriteLine(u.Username);
            IUser u2 = AuthenticationProvider.Authenticate("*", "v2", "quantum");
            Console.WriteLine(u2.Username);
            
            //Card cc = new Card("","");
            //Card[] arrayc = { cc};
            //List<Card> listc = new List<Card>();
            //listc.Add(cc);

            //ICollection ic = arrayc as ICollection;
            //foreach (object o in ic)
            //{
            //    Console.WriteLine(((o as APIDataEntry).ToXMLString()));
            //}

            //ic = listc as ICollection;
            //foreach (object o in ic)
            //{
            //    Console.WriteLine((o as APIDataEntry).ToXMLString());
            //}


            MyClass mc = new MyClass();
            // Console.WriteLine(string.Join(CONST.CARD_SEPERATOR_SYMBOL, mc.ListOfCard.ToArray<string>()));

            //Soi soi = new Soi(1,"ân","lockevn");
            //Console.WriteLine(soi.ToXMLString());


            Console.ReadLine();
        }
    }
}
